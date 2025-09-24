<?php

namespace VanillaCookieConsent\Services;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\HTTPResponse_Exception;
use SilverStripe\Core\Validation\ValidationException;
use VanillaCookieConsent\Models\Insight;

class InsightController extends Controller
{

    private static $allowed_actions = [
        'index',
        'getInsights',
        'save',
    ];

    public function index()
    {
        return $this->renderWith('VanillaCookieConsent/InsightController');
    }

    public function getInsights()
    {
        // This method should return insights data, typically from a model or service.
        // For now, we will return a placeholder response.
        return json_encode([
            'insights' => [
                ['timestamp' => '2023-10-01 12:00:00', 'consentType' => 'Essential'],
                ['timestamp' => '2023-10-01 12:05:00', 'consentType' => 'Analytics'],
                ['timestamp' => '2023-10-01 12:10:00', 'consentType' => 'Marketing'],
            ],
        ]);
    }

    /**
     * @throws HTTPResponse_Exception
     * @throws ValidationException
     */
    public function save(HTTPRequest $request)
    {
        if(!$request->isPOST()) {
            return $this->httpError(405, 'Method Not Allowed');
        }

        $body = $request->getBody();
        if (empty($body)) {
            return $this->httpError(400, 'No data provided.');
        }

        // Decode the JSON body
        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->httpError(400, 'Invalid JSON data: ' . json_last_error_msg());
        }

        // Validate required fields
        if (empty($data['consentType']) || empty($data['acceptedCategories'])) {
            return $this->httpError(400, 'Missing required fields: consentType or acceptedCategories.');
        }

        // Check if consent Type is valid
        $validConsentTypes = ['Accepted', 'Rejected', 'Partly', 'Unknown'];
        if (!in_array($data['consentType'], $validConsentTypes)) {
            // Return error response 400
            return $this->httpError(400, 'Invalid consent type.');
        }

        $insight = Insight::create([
            'Timestamp' => date('Y-m-d H:i:s'),
            'ConsentType' => $data['consentType'],
            'AcceptedCategories' => $data['acceptedCategories'],
        ]);

        if(isset($data['locale'])) {
            $insight->Locale = $data['locale'];
        }

        if(isset($data['subsiteId'])) {
            $insight->SubsiteID = $data['subsiteId'];
        }

        // Save the insight to the database
        if (!$insight->write()) {
            return $this->httpError(500, 'Failed to save insight.');
        }

        // Return success response 201
        return new HTTPResponse(
            null,
            201,
            'Created'
        );
    }
}
