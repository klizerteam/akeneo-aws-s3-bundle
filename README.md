# 🪣 Akeneo AWS S3 Bundle

This Symfony bundle enables AWS S3 integration for file storage in Akeneo PIM.

---

## 📦 Features

- Configure AWS S3 as a file storage option in Akeneo
- Supports Akeneo 6.x/7.x
- Easy installation via Composer
- Interactive setup command for AWS credentials and S3 bucket

---

## 🚀 Installation

### 1. Require the package via Composer

```bash
composer require Klizer/akeneo-aws-s3-bundle
```

### 2. Register the bundle (if not auto-registered)

In `config/bundles.php`, add:

```php
return [
    // ...
    Klizer\AwsS3Bundle\AwsS3Bundle::class => ['all' => true],
];
```

### 3. Run the AWS setup command

```bash
php bin/console klizer:setup:aws-s3
```

This command will prompt for your AWS credentials, region, and S3 bucket name and update Akeneo’s storage configuration.

---

## ⚙️ Configuration

Once the setup command is complete, the bundle will:

- Save AWS credentials securely
- Update Akeneo's Flysystem configuration
- Use AWS S3 as the default backend for media and assets

---

## 🧰 Requirements

- PHP 7.4 or higher
- Akeneo PIM 6.x / 7.x
- Symfony 5 or 6
- AWS account with access to an S3 bucket
