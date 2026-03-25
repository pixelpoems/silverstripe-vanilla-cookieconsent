# Consent Insights

This module includes a built-in analytics feature that tracks user consent decisions and displays visual insights in the CMS.

## Enabling Insights

To enable consent insights, configure the save period in your SiteConfig settings (Settings > Cookie Consent):

1. Navigate to the CMS Settings area
2. Go to the "Cookie Consent" tab
3. Set the "Save Period for Insights (in days)" field to the number of days you want to track (e.g., 30 or 90)
4. Set to 0 to disable insights entirely

## What Gets Tracked

When enabled, the module tracks:
- **Timestamp**: When the consent was given
- **Consent Type**: Whether the user accepted all, rejected all, or partially accepted categories
- **Accepted Categories**: Which specific cookie categories were accepted

## Viewing Insights

Once configured, the Cookie Consent settings tab displays:
- **Visual Charts**: Accept/Reject rate pie chart and category-specific acceptance rates
- **Data Grid**: Detailed list of all consent records from the specified period
- **Locale-specific data**: If using Fluent, insights are broken down by locale

The insights automatically respect Subsites and Fluent locales if those modules are installed.

## Data Management

Old insights are automatically cleaned up based on your configured save period. You can manually clear insights by running:

```bash
sake dev/tasks/VanillaCookieConsent-Tasks-ClearConsentInsightsTask
```

This task will:
- Remove insights older than the configured save period
- Delete all insights if the save period is set to 0 or not configured

**Important**: Consent insights are stored in the database. Make sure you comply with your privacy policy and GDPR requirements when collecting this data.