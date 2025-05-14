<?php

namespace Klizer\AwsS3Bundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class SetupAwsS3Command extends Command
{
    protected static $defaultName = 'klizer:setup:aws-s3';

    protected function configure()
    {
        $this->setDescription('Configure Akeneo to use AWS S3 storage automatically');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectRoot = dirname(__DIR__, 6);
        $envPath = $projectRoot . '/.env.local';

        $output->writeln($projectRoot);

        // Check for required composer packages
        if (!$this->ensureComposerPackageInstalled('league/flysystem-aws-s3-v3', $projectRoot, $output)) {
            return Command::FAILURE;
        }
        if (!$this->ensureComposerPackageInstalled('aws/aws-sdk-php', $projectRoot, $output)) {
            return Command::FAILURE;
        }

        $output->writeln('<info>Setting up AWS credentials with empty values</info>');
        $envVars = [
            'AWS_ACCESS_KEY_ID',
            'AWS_SECRET_ACCESS_KEY',
            'AWS_REGION',
            'AWS_BUCKET_NAME',
            'AWS_PREFIX',
        ];

        $envContent = file_exists($envPath) ? file_get_contents($envPath) : "";

        foreach ($envVars as $key) {
            $envContent = preg_replace("/^$key=.*$/m", '', $envContent); // Remove old value
            $envContent .= "\n$key=";
        }

        file_put_contents($envPath, $envContent);
        $output->writeln('<info>.env.local updated with empty AWS credentials</info>');

        $this->writeOneupConfig($projectRoot, $output);
        $this->writeStorageAliases($projectRoot, $output);
        $this->writeServiceAliases($projectRoot, $output);
        $this->writeKlizerServiceConfig($projectRoot, $output);
        $this->writeKlizerRouteConfig($projectRoot, $output);

        // Clear Akeneo cache
        exec('php bin/console cache:clear --env=prod');
        exec('php bin/console cache:warmup --env=prod');
        $output->writeln('<info>Akeneo cache cleared and warmed up</info>');

        return Command::SUCCESS;
    }

    private function writeOneupConfig($projectRoot, OutputInterface $output)
    {
        $output->writeln('oneup config triggered');
        $dir = $projectRoot . '/vendor/akeneo/pim-community-dev/config/packages/prod';
        $filePath = "$dir/oneup_flysystem.yml";

        $filesystem = new Filesystem();
        if (!$filesystem->exists($dir)) {
            $filesystem->mkdir($dir, 0775);
            $output->writeln("Created directory: $dir");
        }

        $yaml = <<<YAML
oneup_flysystem:
    adapters:
        catalog_storage_adapter:
            awss3v3:
                client: aws_s3_client
                bucket: '%env(AWS_BUCKET_NAME)%'
                prefix: '%env(AWS_PREFIX)%/catalog'
                options:
                    visibility: 'public'

        jobs_storage_adapter:
            awss3v3:
                client: aws_s3_client
                bucket: '%env(AWS_BUCKET_NAME)%'
                prefix: '%env(AWS_PREFIX)%/jobs'
                options:
                    visibility: 'public'

        archivist_adapter:
            awss3v3:
                client: aws_s3_client
                bucket: '%env(AWS_BUCKET_NAME)%'
                prefix: '%env(AWS_PREFIX)%/archive'
                options:
                    visibility: 'public'

        category_storage_adapter:
            awss3v3:
                client: aws_s3_client
                bucket: '%env(AWS_BUCKET_NAME)%'
                prefix: '%env(AWS_PREFIX)%/category'
                options:
                    visibility: 'public'

        catalogs_mapping_adapter:
            awss3v3:
                client: aws_s3_client
                bucket: '%env(AWS_BUCKET_NAME)%'
                prefix: '%env(AWS_PREFIX)%/catalogs_mapping'
                options:
                    visibility: 'public'

        local_storage_adapter:
            local:
                location: '%kernel.project_dir%/var/storage'

    filesystems:
        catalog_storage:
            adapter: 'catalog_storage_adapter'
            mount: 'catalogStorage'

        jobs_storage:
            adapter: 'jobs_storage_adapter'
            mount: 'jobsStorage'

        archivist:
            adapter: 'archivist_adapter'
            mount: 'archivist'

        category_storage:
            adapter: 'category_storage_adapter'
            mount: 'categoryStorage'

        catalogs_mapping:
            adapter: 'catalogs_mapping_adapter'
            mount: 'catalogsMapping'

        local_storage:
            adapter: 'local_storage_adapter'
            mount: 'localStorage'
YAML;

        file_put_contents($filePath, $yaml);
        $output->writeln("oneup_flysystem.yml written to: $filePath");
    }

    private function writeStorageAliases($projectRoot, OutputInterface $output)
    {
        $output->writeln('storage file triggered');
        $dir = $projectRoot . '/vendor/akeneo/pim-community-dev/config/services/prod';
        $filePath = "$dir/storage.yml";

        $filesystem = new Filesystem();
        if (!$filesystem->exists($dir)) {
            $filesystem->mkdir($dir, 0775);
            $output->writeln("Created directory: $dir");
        }

        $yaml = <<<YAML
parameters:
    aws_s3_bucket: '%env(AWS_BUCKET_NAME)%'

services:
    Aws\S3\S3Client:
        arguments:
            -
                version: 'latest'
                region: "%env(AWS_REGION)%"
                credentials:
                    key: "%env(AWS_ACCESS_KEY_ID)%"
                    secret: "%env(AWS_SECRET_ACCESS_KEY)%"
YAML;

        file_put_contents($filePath, $yaml);
        $output->writeln("storage.yml written to: $filePath");
    }

    private function writeServiceAliases($projectRoot, OutputInterface $output)
    {
        $output->writeln('service file triggered');
        $dir = $projectRoot . '/vendor/akeneo/pim-community-dev/config/services';
        $filePath = "$dir/services.yml";

        $aliasBlock = <<<YAML
    aws_s3_client:
        alias: Aws\\S3\\S3Client
YAML;

        $existingContent = file_exists($filePath) ? file_get_contents($filePath) : '';

        if (str_contains($existingContent, 'aws_s3_client:')) {
            $output->writeln("services.yml already contains aws_s3_client alias, skipped writing.");
            return;
        }

        if (preg_match('/^services:\s*$/m', $existingContent) || preg_match('/^services:\s*\n/m', $existingContent)) {
            $lines = explode("\n", $existingContent);
            $newLines = [];
            $servicesFound = false;

            foreach ($lines as $line) {
                $newLines[] = $line;
                if (preg_match('/^services:\s*$/', $line)) {
                    $servicesFound = true;
                } elseif ($servicesFound && trim($line) !== '' && !str_starts_with($line, ' ')) {
                    $newLines[] = rtrim($aliasBlock);
                    $servicesFound = false;
                }
            }

            if ($servicesFound) {
                $newLines[] = rtrim($aliasBlock);
            }

            $updatedContent = implode("\n", $newLines);
            file_put_contents($filePath, $updatedContent);
            $output->writeln("services.yml updated by inserting aws_s3_client alias.");
        } else {
            $block = <<<YAML
services:
$aliasBlock
YAML;
            file_put_contents($filePath, $existingContent . "\n\n" . $block);
            $output->writeln("services.yml created with services block and aws_s3_client alias.");
        }
    }

    private function writeKlizerServiceConfig($projectRoot, OutputInterface $output)
    {
        $filePath = $projectRoot . '/config/services/klizer_aws.yml';
        $dirPath = dirname($filePath);

        $filesystem = new Filesystem();
        if (!$filesystem->exists($dirPath)) {
            $filesystem->mkdir($dirPath, 0775);
            $output->writeln("Created directory: $dirPath");
        }

        $yaml = <<<YAML
aws_s3_client:
    class: Aws\S3\S3Client
    arguments:
        - 
            version: 'latest'
            region: '%env(AWS_REGION)%'
            credentials:
                key: '%env(AWS_ACCESS_KEY_ID)%'
                secret: '%env(AWS_SECRET_ACCESS_KEY)%'
            endpoint: null
            signature_version: 'v4'
            use_path_style_endpoint: false
            bucket: '%env(AWS_BUCKET_NAME)%'
YAML;

        file_put_contents($filePath, $yaml);
        $output->writeln("<info>Service file written to: $filePath</info>");
    }

    private function writeKlizerRouteConfig($projectRoot, OutputInterface $output)
    {
        $filePath = $projectRoot . '/config/routes/klizer_aws.yml';
        $dirPath = dirname($filePath);

        $filesystem = new Filesystem();
        if (!$filesystem->exists($dirPath)) {
            $filesystem->mkdir($dirPath, 0775);
            $output->writeln("Created directory: $dirPath");
        }

        $yaml = <<<YAML
klizer_aws:
    resource: '@KlizerAwsS3Bundle/Resources/config/routes.yml'
YAML;

        file_put_contents($filePath, $yaml);
        $output->writeln("<info>Route file written to: $filePath</info>");
    }

    private function ensureComposerPackageInstalled(string $packageName, string $projectRoot, OutputInterface $output): bool
    {
        $composerLockPath = $projectRoot . '/composer.lock';
        $isInstalled = false;

        if (file_exists($composerLockPath)) {
            $composerLock = json_decode(file_get_contents($composerLockPath), true);
            foreach ($composerLock['packages'] ?? [] as $package) {
                if ($package['name'] === $packageName) {
                    $isInstalled = true;
                    break;
                }
            }
        }

        if (!$isInstalled) {
            $output->writeln("<comment>Package \"$packageName\" not found. Installing...</comment>");
            $cmd = "composer require $packageName";
            $process = proc_open($cmd, [
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ], $pipes, $projectRoot);

            if (is_resource($process)) {
                $stdout = stream_get_contents($pipes[1]);
                $stderr = stream_get_contents($pipes[2]);
                fclose($pipes[1]);
                fclose($pipes[2]);

                $returnCode = proc_close($process);
                if ($returnCode !== 0) {
                    $output->writeln("<error>Failed to install $packageName:</error> $stderr");
                    return false;
                } else {
                    $output->writeln("<info>Successfully installed $packageName</info>");
                    return true;
                }
            }
        } else {
            $output->writeln("<info>$packageName is already installed.</info>");
        }

        return true;
    }
}
