<?php

namespace VanillaCookieConsent\Models;

use SilverStripe\ORM\DataObject;

class Insight extends DataObject
{
    private static $table_name = 'VanillaCookieConsent_Insight';

    private static $singular_name = 'Cookie Consent Insight';

    private static $plural_name = 'Cookie Consent Insights';

    private static $db = [
        'Timestamp' => 'Datetime',
        'ConsentType' => 'Enum("Accepted,Rejected,Partly,Unnown", "Unnown")',
        'AcceptedCategories' => 'Varchar(255)',
    ];

    private static $summary_fields = [
        'Timestamp',
        'ConsentType',
        'AcceptedCategories'
    ];

    private static $default_sort = 'Timestamp DESC';

}
