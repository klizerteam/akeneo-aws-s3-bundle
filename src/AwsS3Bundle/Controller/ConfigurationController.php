<?php
// src/Klizer/AwsS3Bundle/Controller/ConfigurationController.php

namespace Klizer\AwsS3Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfigurationController extends AbstractController
{
    /**
     * @Route("/klizer_aws_configuration", name="klizer_aws_configuration")
     */
    public function index(): Response
    {
        dd("done");
        return $this->render('@klizerAwsS3/configuration/index.html.twig');
    }
}

