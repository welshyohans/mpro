# Environment Configuration

This project now relies on environment variables for sensitive configuration. Populate the variables in your hosting environment or through a local `.env` file that is **never** committed to version control.

## Required variables

| Variable | Description |
| --- | --- |
| `DB_HOST` | Database host name or IP address. |
| `DB_NAME` | Database schema to connect to. |
| `DB_USERNAME` | Database user account. |
| `DB_PASSWORD` | Database user password. |
| `SMS_API_SEND_URL` | HTTPS endpoint for single SMS submissions. |
| `SMS_API_BULK_URL` | HTTPS endpoint for bulk SMS submissions. |
| `SMS_API_TOKEN` | Bearer token issued by the SMS provider. |
| `SMS_SENDER_ID` | Registered sender ID for outbound SMS messages. |

### Optional variables

| Variable | Description |
| --- | --- |
| `SMS_FROM_ID` | Messaging profile identifier used by the SMS provider. |
| `SMS_CALLBACK_URL` | Callback URL for single message status updates. |
| `SMS_STATUS_CALLBACK` | Callback URL for bulk message status updates. |
| `SMS_CREATE_CALLBACK` | Callback URL for bulk message creation events. |
| `SMS_DEFAULT_CAMPAIGN` | Default campaign identifier for bulk messages. |

## Local development

1. Duplicate `.env.example` and rename it to `.env`.
2. Provide real values for each variable. Leave optional values empty if they are not needed.
3. Configure your web server or development environment to export the variables. For example:
   ```bash
   export $(grep -v '^#' .env | xargs)
   ```
4. Restart the PHP runtime so the new environment values are applied.

## Production deployment

* Prefer setting the variables directly in the hosting control panel or web server configuration (e.g., Apache `SetEnv`, Nginx `fastcgi_param`, or systemd service `Environment=` directives).
* Rotate credentials regularly and store them in a secure secrets manager where possible.
* Never commit `.env` files or plaintext secrets to this repository.
