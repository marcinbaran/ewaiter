module.exports = {
    "**/*": "prettier --write --ignore-unknown",
    "**/*.php": [
        "php ./vendor/bin/php-cs-fixer fix --config .php-cs-fixer.php --allow-risky=yes",
    ],
};
