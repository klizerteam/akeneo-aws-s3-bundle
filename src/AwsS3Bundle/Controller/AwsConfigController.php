<?php
namespace Klizer\AwsS3Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
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
        // Initialize the S3 client
        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => $awsValues['AWS_REGION'],
            'credentials' => [
                'key'    => $awsValues['AWS_ACCESS_KEY_ID'],
                'secret' => $awsValues['AWS_SECRET_ACCESS_KEY'],
            ],
        ]);

        // Check bucket existence
        $s3->headBucket([
            'Bucket' => $awsValues['AWS_BUCKET_NAME'],
        ]);

        // If prefix is set, check its existence
        if (!empty($awsValues['AWS_PREFIX'])) {
            $result = $s3->listObjectsV2([
                'Bucket' => $awsValues['AWS_BUCKET_NAME'],
                'Prefix' => rtrim($awsValues['AWS_PREFIX'], '/') . '/',
                'MaxKeys' => 1,
            ]);

            if ($result['KeyCount'] === 0) {
                $awsStatus = 'error';
                $awsError = 'The specified prefix does not exist.';
            } else {
                $awsStatus = 'success';
            }
        } else {
            $awsStatus = 'success';
        }

    } catch (AwsException $e) {
        $awsStatus = 'error';
        $awsError = $e->getAwsErrorMessage() ?: $e->getMessage();
    }

	$response = $this->render('@klizer_aws/index.html.twig', [
	    'form'       => $form->createView(),
	    'nonce'      => $nonce,
	    'aws_status' => $awsStatus,
	    'aws_error'  => $awsError,
	]);
	return $response;

}

    #[Route('/klizer/aws-s3/save', name: 'klizer_aws_s3_save', methods: ['POST'])]
 public function save(Request $request)
{
    // Get all the data from the form submission
    $data = $request->request->all(); // This will retrieve all the form data

    // Check if the required fields exist (adjusted for the nested 'aws_config' array)
    $accessKey = $data['aws_config']['AWS_ACCESS_KEY_ID'] ?? null;
    $secretKey = $data['aws_config']['AWS_SECRET_ACCESS_KEY'] ?? null;
    $bucket = $data['aws_config']['AWS_BUCKET_NAME'] ?? null;
    $region = $data['aws_config']['AWS_REGION'] ?? null;
    $prefix = $data['aws_config']['AWS_PREFIX'] ?? null;

    // Validate if required fields are missing
    if (!$accessKey || !$secretKey || !$bucket || !$region) {
    
        $this->addFlash('error', 'All the fields are Required!');
        // Redirect to the same page with the success message
        return $this->redirect('/#/klizer/aws');
    }

    // Path to .env.local
    $envFile = dirname(__DIR__, 6) . '/.env.local';

    if (!is_writable(dirname($envFile))) {
        return new JsonResponse(['message' => 'Cannot write to .env.local directory'], 500);
    }

    $newEnvVars = [
        'AWS_ACCESS_KEY_ID' => $accessKey,
        'AWS_SECRET_ACCESS_KEY' => $secretKey,
        'AWS_BUCKET_NAME' => $bucket,
        'AWS_REGION' => $region,
        'AWS_PREFIX' => $prefix, // Include AWS_PREFIX
    ];

    $envContent = file_exists($envFile) ? file_get_contents($envFile) : '';

	foreach ($newEnvVars as $key => $value) {
	    $pattern = "/^$key=.*$/m";
	    $replacement = "$key=$value"; // No quotes added

	    if (preg_match($pattern, $envContent)) {
		$envContent = preg_replace($pattern, $replacement, $envContent);
	    } else {
		$envContent .= "\n$replacement";
	    }
	}


    file_put_contents($envFile, $envContent);

   // Add a flash message for success
    $this->addFlash('success', 'AWS credentials saved successfully.');

    // Redirect back to the AWS configuration page
   return $this->redirect('/#/klizer/aws');
}


    // Helper method to extract environment variables from .env.local content
    private function getEnvValue(string $envContent, string $key): ?string
{
    if (preg_match("/^{$key}\s*=\s*(.*)$/m", $envContent, $matches)) {
        $value = trim($matches[1]);
        // Remove surrounding single or double quotes if they exist
        return preg_replace('/^["\']?(.*?)["\']?$/', '$1', $value);
    }

    return null;
}

}

