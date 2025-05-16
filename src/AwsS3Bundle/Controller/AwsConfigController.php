<?php

namespace Klizer\AwsS3Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Klizer\AwsS3Bundle\Form\AwsConfigType;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class AwsConfigController extends AbstractController
{
    #[Route('/klizer/aws', name: 'klizer_aws')]
    public function index(Request $request): Response
    {
        $nonce = bin2hex(random_bytes(16));
        $envFile = dirname(__DIR__, 6) . '/.env.local';
        $envContent = file_exists($envFile) ? file_get_contents($envFile) : '';

        $awsValues = array_map('trim', [
            'AWS_ACCESS_KEY_ID'     => $this->getEnvValue($envContent, 'AWS_ACCESS_KEY_ID'),
            'AWS_SECRET_ACCESS_KEY' => $this->getEnvValue($envContent, 'AWS_SECRET_ACCESS_KEY'),
            'AWS_BUCKET_NAME'       => $this->getEnvValue($envContent, 'AWS_BUCKET_NAME'),
            'AWS_REGION'            => $this->getEnvValue($envContent, 'AWS_REGION'),
            'AWS_PREFIX'            => $this->getEnvValue($envContent, 'AWS_PREFIX'),
        ]);

        $form = $this->createForm(AwsConfigType::class, $awsValues);
        $form->handleRequest($request);

        $awsStatus = 'unknown';
        $awsError = null;

        try {
            $s3 = new S3Client([
                'version'     => 'latest',
                'region'      => $awsValues['AWS_REGION'],
                'credentials' => [
                    'key'    => $awsValues['AWS_ACCESS_KEY_ID'],
                    'secret' => $awsValues['AWS_SECRET_ACCESS_KEY'],
                ],
            ]);

            $s3->headBucket(['Bucket' => $awsValues['AWS_BUCKET_NAME']]);

            if (!empty($awsValues['AWS_PREFIX'])) {
                $result = $s3->listObjectsV2([
                    'Bucket' => $awsValues['AWS_BUCKET_NAME'],
                    'Prefix' => rtrim($awsValues['AWS_PREFIX'], '/') . '/',
                    'MaxKeys' => 1,
                ]);

                $awsStatus = $result['KeyCount'] === 0 ? 'error' : 'success';
                $awsError = $result['KeyCount'] === 0 ? 'The specified prefix does not exist.' : null;
            } else {
                $awsStatus = 'success';
            }
        } catch (AwsException $e) {
            $awsStatus = 'error';
            $awsError = $e->getAwsErrorMessage() ?: $e->getMessage();
        }

        return $this->render('@klizer_aws/index.html.twig', [
            'form'       => $form->createView(),
            'nonce'      => $nonce,
            'aws_status' => $awsStatus,
            'aws_error'  => $awsError,
        ]);
    }

    #[Route('/klizer/aws-s3/save', name: 'klizer_aws_s3_save', methods: ['POST'])]
    public function save(Request $request): Response
    {
        $data = $request->request->all();

        $accessKey = $data['aws_config']['AWS_ACCESS_KEY_ID'] ?? null;
        $secretKey = $data['aws_config']['AWS_SECRET_ACCESS_KEY'] ?? null;
        $bucket    = $data['aws_config']['AWS_BUCKET_NAME'] ?? null;
        $region    = $data['aws_config']['AWS_REGION'] ?? null;
        $prefix    = $data['aws_config']['AWS_PREFIX'] ?? null;

        if (!$accessKey || !$secretKey || !$bucket || !$region) {
            $this->addFlash('error', 'All the fields are required!');
            return $this->redirectToRoute('klizer_aws');
        }

        $envFile = dirname(__DIR__, 6) . '/.env.local';

        if (!is_writable(dirname($envFile))) {
            return new JsonResponse(['message' => 'Cannot write to .env.local directory'], 500);
        }

        $newEnvVars = [
            'AWS_ACCESS_KEY_ID'     => $accessKey,
            'AWS_SECRET_ACCESS_KEY' => $secretKey,
            'AWS_BUCKET_NAME'       => $bucket,
            'AWS_REGION'            => $region,
            'AWS_PREFIX'            => $prefix,
        ];

        $envContent = file_exists($envFile) ? file_get_contents($envFile) : '';

        foreach ($newEnvVars as $key => $value) {
            $pattern = "/^$key=.*$/m";
            $replacement = "$key=$value";
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n$replacement";
            }
        }

        file_put_contents($envFile, $envContent);

        $this->addFlash('success', 'AWS credentials saved successfully.');
        return $this->redirectToRoute('klizer_aws');
    }

    /**
     * Helper method to extract environment variable value from .env content.
     *
     * @param string $envContent The full .env.local file content
     * @param string $key        The key to look up
     *
     * @return string|null The value of the key or null if not found
     */
    private function getEnvValue(string $envContent, string $key): ?string
    {
        if (preg_match("/^{$key}\s*=\s*(.*)$/m", $envContent, $matches)) {
            return preg_replace('/^["\']?(.*?)["\']?$/', '$1', trim($matches[1]));
        }

        return null;
    }
}
