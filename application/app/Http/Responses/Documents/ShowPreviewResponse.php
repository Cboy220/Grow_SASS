<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [show] process for the proposals
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Documents;
use Illuminate\Contracts\Support\Responsable;

class ShowPreviewResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for team members
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        $payload = $this->payload;

        //print-mode
        if (request('render') == 'print') {
            config(['visibility.page_rendering' => 'print-page']);
        } else {
            config(['visibility.page_rendering' => 'view']);
        }

        //generate the estimate
        if ($has_estimate) {
            $rendered_estimate = view('pages/bill/bill-embed', compact('page', 'bill', 'taxrates', 'taxes', 'elements', 'units', 'lineitems', 'customfields', 'estimate'))->render();
        }

        //render the page
        $final_document = view('pages/documents/preview/page', compact('page', 'document', 'payload', 'customfields', 'estimate'))->render();

        //add estimate
        if ($has_estimate) {
            $final_document = str_replace('{pricing_table}', $rendered_estimate, $final_document);
        } else {
            $final_document = str_replace('{pricing_table}', '', $final_document);
        }

        //replace all other variables
        if ($document->doc_type == 'proposal') {
            $final_document = str_replace('{company_name}', config('system.settings_company_name'), $final_document);
            $final_document = str_replace('{proposal_id}', $document->doc_id, $final_document);
            $final_document = str_replace('{title}', $document->doc_title, $final_document);
            $final_document = str_replace('{proposal_date}', runtimeDate($document->doc_date_start), $final_document);
            $final_document = str_replace('{expiry_date}', runtimeDate($document->doc_date_end), $final_document);
            $final_document = str_replace('{prepared_by_name}', $document->first_name . ' ' . $document->last_name, $final_document);
            $final_document = str_replace('{pricing_total}', runtimeMoneyFormat(isset($bill) ? $bill->bill_final_amount : 0), $final_document);
            $final_document = str_replace('{todays_date}', runtimeDate(now()), $final_document);
        }

        //replace all other variables
        if ($document->doc_type == 'contract') {
            $final_document = str_replace('{company_name}', config('system.settings_company_name'), $final_document);
            $final_document = str_replace('{contract_id}', $document->doc_id, $final_document);
            $final_document = str_replace('{contract_title}', $document->doc_title, $final_document);
            $final_document = str_replace('{contract_date}', runtimeDate($document->doc_date_start), $final_document);
            $final_document = str_replace('{contract_end_date}', runtimeDate($document->doc_date_end), $final_document);
            $final_document = str_replace('{prepared_by_name}', $document->first_name . ' ' . $document->last_name, $final_document);
            $final_document = str_replace('{pricing_table_total}', runtimeMoneyFormat(isset($bill) ? $bill->bill_final_amount : 0), $final_document);
            $final_document = str_replace('{contract_value}', runtimeMoneyFormat($document->doc_value), $final_document);
            $final_document = str_replace('{todays_date}', runtimeDate(now()), $final_document);

            $final_document = str_replace('{client_company_name}', $document->client_company_name, $final_document);
            $final_document = str_replace('{client_phone}', $document->client_phone, $final_document);
            $final_document = str_replace('{client_street}', $document->client_billing_street, $final_document);
            $final_document = str_replace('{client_city}', $document->client_billing_city, $final_document);
            $final_document = str_replace('{client_state}', $document->client_billing_state, $final_document);
            $final_document = str_replace('{client_zip}', $document->client_billing_zip, $final_document);
            $final_document = str_replace('{client_country}', $document->client_billing_country, $final_document);
            $final_document = str_replace('{client_website}', $document->client_website, $final_document);


        }

        if ($document->docresource_type == 'lead') {
            $final_document = str_replace('{client_company_name}', $document->lead_company_name, $final_document);
            $final_document = str_replace('{client_first_name}', $document->lead_firstname, $final_document);
            $final_document = str_replace('{client_last_name}', $document->lead_lastname, $final_document);
        } else {
            $final_document = str_replace('{client_company_name}', $document->client_company_name, $final_document);
            $final_document = str_replace('{client_first_name}', $document->client_first_name, $final_document);
            $final_document = str_replace('{client_last_name}', $document->client_last_name, $final_document);
        }

        //show page
        return $final_document;
    }

}
