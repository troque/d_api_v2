<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RadicacionFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $anonymous = [
            "data.attributes.correspondence" => ["required", "array"],
            "data.attributes.correspondence.documentType" => ["required"],
            "data.attributes.correspondence.description" => ["required"],
            "data.attributes.correspondence.attachDocument" => ["required", "boolean"],
            "data.attributes.correspondence.comunicationType" => ["required"],
            "data.attributes.correspondence.digitazionRequired" => ["required", "boolean"],
            "data.attributes.correspondence.electronicDistribution" => ["required", "boolean"],
            "data.attributes.correspondence.physicDistribution" => ["required", "boolean"],
            "data.attributes.correspondence.receptionChannel" => ["required"],
            "data.attributes.correspondence.requestSubsection" => ["required"],
            "data.attributes.listAgents" => ["required", "array"],
            "data.attributes.listAgents.0.agentType" => ["required"],
            "data.attributes.listAgents.0.personType" => ["required"],
            "data.attributes.listAgents.1.agentType" => ["required"],
            "data.attributes.listAgents.1.identificationNumber" => ["required", "integer"],
            "data.attributes.documentList.*.subject" => ["required"],
            "data.attributes.documentList.*.foliosNumber" => ["required", "integer"],
            "data.attributes.documentList.*.attachmentNumber" => ["required", "integer"],
            "data.attributes.documentList.*.documentDate" => ["required", "date"],
            "data.attributes.documentList.*.documentType" => ["required"],
            "data.attributes.referencedList" => ["array"],
            "data.attributes.attachmentList" => ["array"],
        ];

        $natural = [];
        if ($this->input("data.attributes.listAgents.0.personType", "") === "Persona Natural") {
            $natural = [
                "data.attributes.listAgents.0.identificationNumber" => ["required", "integer"],
                "data.attributes.listAgents.0.name" => ["required"],
                "data.attributes.listAgents.0.person" => ["required", "array"],
                "data.attributes.listAgents.0.person.contactList" => ["required", "array"],
                "data.attributes.listAgents.0.person.contactList.*.IsPrincipal" => ["required", "boolean"],
                "data.attributes.listAgents.0.person.contactList.*.address" => ["required"],
                "data.attributes.listAgents.0.person.contactList.*.cellphone" => ["required"],
                "data.attributes.listAgents.0.person.contactList.*.contactType" => ["required"],
                "data.attributes.listAgents.0.person.contactList.*.country" => ["required"],
                "data.attributes.listAgents.0.person.contactList.*.department" => ["required"],
                "data.attributes.listAgents.0.person.contactList.*.email" => ["required", "email"],
                "data.attributes.listAgents.0.person.contactList.*.municipality" => ["nullable"],
                "data.attributes.listAgents.0.person.contactList.*.phone" => ["required", "integer"],
                "data.attributes.listAgents.0.person.identificationNumber" => ["required", "integer"],
                "data.attributes.listAgents.0.person.identificationType" => ["required"],
                "data.attributes.listAgents.0.person.name" => ["required"],
                "data.attributes.listAgents.0.person.personType" => ["required"],
            ];
        }
        return array_merge($anonymous, $natural);
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}
