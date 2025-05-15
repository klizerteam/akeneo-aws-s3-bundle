# 🪣 Akeneo AWS S3 Bundle

A Symfony bundle that integrates **Amazon S3** as a storage backend for **Akeneo PIM**.

---

## 📦 Features

- ✅ Configure AWS S3 for storing media and asset files in Akeneo
- ⚙️ Compatible with **Akeneo PIM 6.x and 7.x**
- 🔄 Automatically updates Flysystem configuration
- 🔐 Uses environment variables or configuration files for AWS credentials
- 🧩 Simple Composer-based installation

---

## 🚀 Installation

### 1. Install via Composer

Run the following command in your Akeneo root directory:

```bash
composer require klizer/akeneo-aws-s3-bundle
```

---

### 2. Register the Bundle (if not auto-registered)

In `config/bundles.php`, add:

```php
return [
    // ...
    Klizer\AwsS3Bundle\AwsS3Bundle::class => ['all' => true],
];
```

---

### 3. Configure Autoloading (if needed)

In your project’s root `composer.json`, add:

```json
"autoload": {
  "psr-4": {
    "Klizer\\AwsS3Bundle\\": "vendor/klizer/akeneo-aws-s3-bundle/src/AwsS3Bundle/"
  }
}
```

Then dump the autoloader:

```bash
composer dump-autoload
```

---

### 4. Run the Setup Command

```bash
php bin/console klizer:setup:aws-s3
```

This will:

- Validate your AWS environment configuration
- Generate necessary service files
- Update Flysystem settings to use AWS S3

---

## ⚙️ Configuration Summary

Once setup is complete:

- AWS credentials are pulled from environment variables
- Flysystem config is auto-updated to use AWS S3
- Media and assets are stored and retrieved from S3 seamlessly

---

## 🧰 Requirements

- PHP **7.4+**
- Akeneo **7.x**
- Symfony **5.x / 6.x**
- AWS S3 account with programmatic access (IAM user)

---

## 👨‍🔧 Maintainer

**Klizer Development Team**  
📫 [prakashs@klizer.com](mailto:prakashs@klizer.com)
