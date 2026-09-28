<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portal;
use App\Models\Property;
use DOMDocument;
use DOMImplementation;
use DOMNode;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class XMLFeedV2Controller extends Controller
{
    private static $xml = null;
    private $last_update = null;
    private $listing_count = null;

    /**
     * @var string
     */
    private static $encoding = 'UTF-8';

    /**
     * Convert an Array to XML.
     *
     * @param  string  $node_name  - name of the root node to be converted
     * @param  array  $arr  - array to be converted
     * @param  array  $docType  - optional docType
     * @param  array  $pfObj  - array to be converted
     *
     * @return DomDocument
     * @throws Exception
     */
    public static function createXML($pfObj = [], $node_name, $arr = [], $docType = [], $encoding = 'UTF-8')
    {
        $xml = self::getXMLRoot($encoding);

        if ($docType) {
            $xml->appendChild((new DOMImplementation())
                    ->createDocumentType($docType['name'] ?? '', $docType['publicId'] ?? '', $docType['systemId'] ?? ''));
        }

        $xml->appendChild(self::convert($node_name, $arr, $pfObj));
        self::$xml = null; // clear the xml node in the class for 2nd time use.
        return $xml;
    }

    /**
     * Initialize the root XML node [optional].
     *
     * @param  string  $version
     * @param  string  $encoding
     * @param  bool  $standalone
     * @param  bool  $format_output
     */
    public static function init($version = '1.0', $encoding = 'utf-8', $standalone = false, $format_output = true)
    {
        self::$xml = new DomDocument($version, $encoding);
        self::$xml->xmlStandalone = $standalone;
        self::$xml->formatOutput = $format_output;
        self::$encoding = $encoding;
    }

    /**
     * Get string representation of boolean value.
     *
     * @param  mixed  $v
     *
     * @return string
     */
    private static function bool2str($v)
    {
        //convert boolean to text value.
        $v = $v === true ? 'true' : $v;
        $v = $v === false ? 'false' : $v;

        return $v;
    }

    /**
     * Convert an Array to XML.
     *
     * @param  string  $node_name  - name of the root node to be converted
     * @param  array  $arr  - array to be converted
     *
     * @return DOMNode
     *
     * @throws Exception
     */
    private static function convert($node_name, $arr = [], $pfObj = [],$encoding = 'UTF-8')
    {
        //dd($node_name,$pfObj);
        $xml = self::getXMLRoot($encoding);
        $node = $xml->createElement($node_name);
        if ($node_name == 'list') {
            $node->setAttribute('last_update', $pfObj['last_update'] ?? '');
            $node->setAttribute('listing_count', $pfObj['listing_count'] ?? '');
        }

        if (is_array($arr)) {
            // get the attributes first.;
            if (array_key_exists('@attributes', $arr) && is_array($arr['@attributes'])) {
                foreach ($arr['@attributes'] as $key => $value) {
                    if (!self::isValidTagName($key)) {
                        throw new Exception('[Array2XML] Illegal character in attribute name. attribute: ' . $key . ' in node: ' . $node_name);
                    }
                    $node->setAttribute($key, self::bool2str($value));
                }
                unset($arr['@attributes']); //remove the key from the array once done.

            }

            // check if it has a value stored in @value, if yes store the value and return
            // else check if its directly stored as string
            if (array_key_exists('@value', $arr)) {
                $node->appendChild($xml->createTextNode(self::bool2str($arr['@value'])));
                unset($arr['@value']); //remove the key from the array once done.
                //return from recursion, as a note with value cannot have child nodes.
                return $node;
            } elseif (array_key_exists('@cdata', $arr)) {
                $node->appendChild($xml->createCDATASection(self::bool2str($arr['@cdata'])));
                unset($arr['@cdata']); //remove the key from the array once done.
                //return from recursion, as a note with cdata cannot have child nodes.
                return $node;
            }
        }

        //create subnodes using recursion
        if (is_array($arr)) {
            // recurse to get the node for that key
            foreach ($arr as $key => $value) {
                if (!self::isValidTagName($key)) {
                    throw new Exception('[Array2XML] Illegal character in tag name. tag: ' . $key . ' in node: ' . $node_name);
                }
                if (is_array($value) && is_numeric(key($value))) {
                    // MORE THAN ONE NODE OF ITS KIND;
                    // if the new array is numeric index, means it is array of nodes of the same kind
                    // it should follow the parent key name
                    foreach ($value as $k => $v) {
                        $node->appendChild(self::convert($key, $v));
                    }
                } else {
                    // ONLY ONE NODE OF ITS KIND
                    $node->appendChild(self::convert($key, $value));
                }
                unset($arr[$key]); //remove the key from the array once done.

            }
        }

        // after we are done with all the keys in the array (if it is one)
        // we check if it has any text value, if yes, append it.
        if (!is_array($arr)) {
            $node->appendChild($xml->createTextNode(self::bool2str($arr)));
        }

        return $node;
    }

    /**
     * Get the root XML node, if there isn't one, create it.
     *
     * @return DomDocument|null
     */
    private static function getXMLRoot($encoding = 'UTF-8')
    {
        if (empty(self::$xml)) {
            self::init($version = '1.0', $encoding, $standalone = false, $format_output = true);
        }

        return self::$xml;
    }

    /**
     * Check if the tag name or attribute name contains illegal characters
     * Ref: http://www.w3.org/TR/xml/#sec-common-syn.
     *
     * @param  string  $tag
     *
     * @return bool
     */
    private static function isValidTagName($tag)
    {
        $pattern = '/^[a-z_]+[a-z0-9\:\-\.\_]*[^:]*$/i';

        return preg_match($pattern, $tag, $matches) && $matches[0] == $tag;
    }

    public function generateDubizzleBayutXMLFile()
    {
        $mainPortal = Portal::where("name", "Bayut")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        // abort_if($mainPortal->xml_url != $request->key, 404);

        $prop = [];

        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id)->orWhere('portal_id', 3);
        })
            ->where('is_live', true)
            // ->whereHas('agent', function ($q) {
            //     $q->where('is_commercial', false);
            // })
            ->whereHas('community.region', function ($q) {
                $q->where('name', 'Dubai');
            })
            ->with([
                'agent' => function ($q) {
                    $q->select('id', 'name', 'first_name', 'last_name', 'pp_enabled', 'pp_name', 'pp_photo', 'pp_license_no', 'email', 'secondary_email', 'license_no', 'mobile', 'bio', 'thumbnail', 'image');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id');
                },
                'propertyCategory.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title', 'property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                }, 'developer',
                'price' => function ($q) {
                    $q->select('id', 'property_id', 'price', 'prices', 'price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                },
                'bath.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                },
                'amenities' => function ($q) use ($mainPortal) {
                    $q->whereHas('amenity.portals', function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id);
                    });
                },
                'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', '=', $mainPortal->id)
                        ->where('title', '!=', '');
                },
                'community.region', 'subCommunity', 'communityProperty', 'portals',
                'photos' => function ($q) {
                    $q->select('id', 'property_id', 'path', 'title', 'updated_at');
                },
                'floorPlan'])
            ->orderBy('properties.created_at', 'desc')
            ->get();

        foreach ($pp as $key => $value) {
            $data = array();
            $data['Property_Ref_No'] = $value?->ref_no;

            $data['Rent_Frequency']['@cdata'] = '';
            if ($value?->for == "Residential for sale" || $value?->for == "Commercial for sale") {
                $data['Property_purpose']['@cdata'] = 'Buy';
            } else {
                $data['Property_purpose']['@cdata'] = 'Rent';
                $data['Rent_Frequency']['@cdata'] = 'yearly';
            }
            $data['Property_Type']['@cdata'] = $value?->propertyCategory?->portals?->first()?->title ?? '';
            //$data['Property_Type']['@cdata'] = $value['property_category']['portals'][0]['title'] ?? '';
            $data['Property_Status']['@cdata'] = 'live';

            $data['City']['@cdata'] = $value?->community?->region?->name ?? 'Dubai';
            $data['Locality']['@cdata'] = $value?->community?->alternate_name ?? $value?->community?->name ?? '';
            $data['Sub_Locality']['@cdata'] = $value?->subCommunity?->alternate_name ?? $value?->subCommunity?->name ?? '';
            if (!$value?->communityProperty?->alternate_use) {
                $data['Tower_Name']['@cdata'] = '';
            } else {
                $data['Tower_Name']['@cdata'] = $value?->communityProperty?->alternate_name ?? $value?->communityProperty?->name ?? '';
            }
            $data['Property_Title']['@cdata'] = $value->title ?? '';
            //$data['Property_Description']['@cdata'] = $value['description'] ?? '';
            $description = strip_tags($value->description, '<br>');
            $updateDescription = str_replace("<br />", "\r\n", $description);
            $data['Property_Description']['@cdata'] = str_replace("<br>", "\r\n", $updateDescription) ?? '';
            if ($value->furnished == 'FURNISHED') {
                $data['Furnished']['@cdata'] = 'Yes';
            } else {
                if ($value['furnished'] == 'SEMI_FURNISHED') {
                    $data['Furnished']['@cdata'] = 'Partly';
                } else {
                    $data['Furnished']['@cdata'] = 'No';
                }
            }

            $data['Property_Size']['@cdata'] = str_replace(',', '', $value['built_up_area'] . '') ?? '';
            $data['Property_Size_Unit']['@cdata'] = "SQFT";

            $data['bedroom'] = $value?->bed?->portals?->first()?->name ?? 0;
            $data['bathroom'] = $value?->bath?->portals->first()->name ?? 0;

            $data['Price']['@cdata'] = ($value?->price?->price ?? '') . '';
            if ($value['agent']) {
                $data['Listing_Agent']['@cdata'] = $value?->agent?->first_name . ' ' . $value?->agent?->last_name;

                $data['Listing_Agent_Email']['@cdata'] = $value?->agent?->secondary_email != "" ? $value?->agent?->secondary_email : $value?->agent?->email;
                if ($value['agent']['pp_enabled']) {
                    $data['Listing_Agent']['@cdata'] = $value?->agent?->pp_name;
                }
                $data['Listing_Agent_Phone']['@cdata'] = $value?->agent?->mobile;
            }
            $data['Features']['Feature'] = array();
            foreach ($value["amenities"] ?? [] as $amenity) {

                $data['Features']['Feature'][]['@cdata'] = $amenity?->amenity?->portals?->first()?->title;
                // foreach ($amenity["amenity"]["portals"] ?? [] as $portal) {
                //     if ($portal["portal_id"] == $mainPortal->id && !empty($portal["title"])) {
                //         $data['Features']['Feature'][]['@cdata'] = $portal["title"];
                //     }
                // }
            }

            $data['Images'] = [];

            if ($value['photos']) {
                foreach ($value->photos ?? [] as $key => $photo) {
                    $data['Images']['Image'][]['@cdata'] = $photo?->path;
                }
            }

            $data['Videos']['Video']['@cdata'] = $value?->video_link;
            $data['Last_Updated']['@cdata'] = date('Y-m-d H:i:s', strtotime($value?->updated_at));

            $data['Permit_Number']['@cdata'] = $value['rera_permit_number'] ?? '';

            // Test

            // $data['offplanDetails_saleType'] = '';
            if ($value?->completion_status == 'Off-Plan Primary' || $value?->completion_status == 'Off-Plan Secondary') {
                $data['Off_Plan']['@cdata'] = 'Yes';

                if($value?->completion_status == 'Off-Plan Primary'){
                    $data['offplanDetails_saleType'] = 'New';
                }elseif($value?->completion_status == 'Off-Plan Secondary'){
                    $data['offplanDetails_saleType'] = 'Resale';
                }
            } else {
                $data['Off_Plan']['@cdata'] = 'No';
            }
            $data['Portals'] = [];

            foreach ($value['portals'] as $portal) {
                if ($portal['portal_id'] == 3) {
                    $data['Portals']['Portal'][]['@cdata'] = 'Dubizzle';
                }
                if ($portal['portal_id'] == 2) {
                    $data['Portals']['Portal'][]['@cdata'] = 'Bayut';
                }
            }

            $prop['Property'][] = $data;
        } // end foreach

        $xml = $this->createXML([], 'Properties', $prop);
        $xmlContent = $xml->saveXML();

        // Save XML to a file
        $filePath = storage_path('app/public/xml/bayut_dubizzle.xml');
        file_put_contents($filePath, $xmlContent);

        return response()->json(['message' => 'XML file generated successfully', 'file_path' => $filePath]);
    }

    public function getDubizzleBayutXMLDataFromFile(Request $request)
    {
        info('DubizzleBayut is called at ' . now());
        $mainPortal = Portal::where("name", "Bayut")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        abort_if($mainPortal->xml_url != $request->key, 404);

        $filePath = storage_path('app/public/xml/bayut_dubizzle.xml');

        if (!file_exists($filePath)) {
            abort(404, 'XML file not found');
        }

        $xmlContent = file_get_contents($filePath);

        return response($xmlContent, 200)->header('Content-Type', 'text/xml');
    }

    public function generatePropertyFinderXMLFile()
    {
        try {
            $mainPortal = Portal::where("slug", "property-finder")->latest()->first();
            if (is_null($mainPortal)) {
                abort(404);
            }
            // abort_if($mainPortal->xml_url != $key, 404);

            $prop = [];
            $completionStatusMapping = [
                'Ready Primary' => 'completed_primary',
                'Ready Secondary' => 'completed',
                'Off-Plan Primary' => 'off_plan_primary',
                'Off-Plan Secondary' => 'off_plan',
            ];

            $furnishedMapping = [
                'FURNISHED' => 'Yes',
                'SEMI_FURNISHED' => 'Partly',
            ];

            $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
                ->where('is_live', true)
                ->where('region_id', 1) // Dubai Region id
                ->whereHas('agent', function ($q) {
                    $q->where('is_commercial', false);
                })
                ->with([
                    'agent' => function ($q) {
                        $q->select('id', 'name', 'first_name', 'last_name', 'pp_enabled', 'pp_name', 'pp_photo', 'pp_license_no', 'email', 'secondary_email', 'license_no', 'mobile', 'bio', 'thumbnail', 'image');
                    },
                    'details',
                    'propertyCategory' => function ($q) {
                        $q->select('id');
                    },
                    'propertyCategory.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'title', 'property_category_id', 'portal_id');
                    },
                    'bed' => function ($q) {
                        $q->select('id');
                    },
                    'bed.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                    },
                    'developer',
                    'price' => function ($q) {
                        $q->select('id', 'property_id', 'price', 'prices', 'price_on_application');
                    },
                    'bath' => function ($q) {
                        $q->select('id');
                    },
                    'bath.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                    },
                    'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function ($q) use ($mainPortal) {
                            $q->where('portal_id', '=', $mainPortal->id);
                        });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                            ->where('title', '!=', '');
                    },
                    'community.region', 'subCommunity', 'communityProperty',
                    'photos' => function ($q) {
                        $q->select('id', 'property_id', 'path', 'title', 'updated_at');
                    },
                    'floorPlan'])
                ->orderBy('properties.created_at', 'desc')
                ->get();

            $listing_count = $pp->count();
            if ($listing_count == 0) {
                info('No listings found for Property Finder XML V2');
                return response()->json(['error' => 'No listings found'], 404);
            }
            $mostRecentListing = $pp->sortByDesc('updated_at')->first();

            foreach ($pp as $key => $value) {
                $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($mostRecentListing->updated_at))];
                $data['reference_number'] = $value['ref_no'];
                $data['permit_number'] = $value['rera_permit_number'] ?? '';
                $data['price'] = [];
                if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                    $data['price'] = $value['price']["price"] ?? 0;
                    $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
                } else {
                    $data['price']['yearly'] = $value['price']["price"] ?? 0;
                    $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
                    $data['cheques'] = $value['cheques'] ?? null;
                }
                $data['property_type'] = $value['propertyCategory']['portals'][0]["title"] ?? "";
                $data['price_on_application'] = $value['price']['price_on_application'] ? 'Yes' : 'No';

                $data['availability_date'] = $value?->availability_date;
                // $data['title_deed']['@cdata'] = $value?->title_deed_text ?? '';
                $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
                $data['community']['@cdata'] = $value['community']['name'] ?? '';
                $data['sub_community']['@cdata'] = $value['subCommunity']['name'] ?? '';

                if (($value?->communityProperty?->name ?? null) && (!$value?->communityProperty?->default_use)) {
                    $data['property_name']['@cdata'] = '';
                } else {
                    $data['property_name']['@cdata'] = $value['communityProperty']['name'] ?? '';
                }

                $data['title_en']['@cdata'] = $value['title'] ?? '';
                //$data['description_en']['@cdata'] = $value['description'] ?? '';

                $description = strip_tags($value['description'], '<br>');
                $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $description) ?? '';

                $data['completion_status'] = $completionStatusMapping[$value['completion_status']] ?? 'completed';
                $data['floor'] = $value['floor'] ?? '';
                $data['parking'] = $value['parking'] ?? 0;
                $data['furnished'] = $furnishedMapping[$value['furnished']] ?? 'No';
                $data['size'] = $value['built_up_area'] ?? '';
                $data['plot_size'] = $value['plot_size'] ?? '';
                $data['build_year'] = $value['build_year'] ?? '';

                $data['payment_method'] = $value['payment_method'] ?? '';

                if ($value['payment_method'] == "installments" || $value['payment_method'] == "cash,installments") {
                    $data['down_payment_price'] = $value['down_payment_price'] ?? '';
                }
                $data['bedroom'] = $value["bed"]["portals"][0]["name"] ?? 0;
                $data['bathroom'] = $value["bath"]["portals"][0]["name"] ?? 0;
                $data['view'] = $value['view'] ?? '';
                $privateAmenities = [];
                $commercialAmenities = [];
                $features = [];

                foreach ($value["amenities"] ?? [] as $amenity) {
                    $amenityType = $amenity["amenity"]["amenity_type"] ?? [];
                    if ($amenityType == "private") {
                        $privateAmenities[] = $amenity["amenity"]["portals"][0]['title'];
                    }
                    if ($amenityType == "commercial") {
                        $commercialAmenities[] = $amenity["amenity"]["portals"][0]['title'];
                    }
                    $features[] = $amenity["amenity"]["portals"][0]["title"];
                }
                $data['private_amenities'] = implode(",", $privateAmenities);
                $data['commercial_amenities'] = implode(",", $commercialAmenities);
                $data['amenities'] = implode(',', $features);

                //$data['view'] = $value['view'] ?? '';

                if ($value['agent']) {
                    $data['agent']['id'] = $value['agent']['id'];

                    if ($value['agent']['pp_enabled']) {
                        $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                        $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                        $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? '';
                    } else {
                        $data['agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                        $data['agent']['photo'] = $value['agent']['image_url'] ?? '';
                        $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                    }
                    if ($value['agent']['secondary_email'] != "") {
                        $data['agent']['email'] = $value['agent']['secondary_email'] ?? '';
                    } else {
                        $data['agent']['email'] = $value['agent']['email'] ?? '';
                    }
                    $number = str_replace("+971", "971", $value['agent']['mobile']) ?? '';
                    $data['agent']['phone'] = str_replace(' ', '', $number);
                    $data['agent']['info'] = $value['agent']['bio'] ?? '';

                }
                $data['developer'] = $value['developer']['short_name'] ?? $value['details']['developer'] ?? null;
                $data['photo'] = [];
                if ($value['photos']) {
                    $url = [];
                    foreach ($value['photos'] as $key => $photo) {
                        $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s', strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                        $dd['@value'] = $photo['path'];
                        $url['url'][] = $dd;
                    }
                    $data['photo'] = $url;
                }

                $data['video_tour_url'] = $value['video_link'] ?? '';
                $data['view360'] = $value['video_360_link'] ?? '';

                $data['floor_plan'] = [];

                if ($value['floor_plan']) {
                    $url = [];
                    $dd['@attributes'] = ['last_updated' => $value['floor_plan']['updated_at'], 'watermark' => 'no'];
                    $dd['@value'] = $value['floor_plan']['path'] ?? null;
                    $url['url'][] = $dd;
                    $data['floor_plan'] = $url;
                }

                $prop['property'][] = $data;
                if (!empty($value["communityProperty"])) {
                    $data['geopoints'] = $value["communityProperty"]["latitude"] . ", " . $value["communityProperty"]["longitude"];
                } elseif (!empty($value["subCommunity"])) {
                    $data['geopoints'] = $value["subCommunity"]["latitude"] . ", " . $value["subCommunity"]["longitude"];
                } else {
                    $data['geopoints'] = ''; //$value['latitude'] . ',' . $value['longitude'];
                }
            }

            $last_update = $mostRecentListing->updated_at;
            $this->listing_count = $listing_count;
            $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update));
            $pfObj['listing_count'] = $listing_count;

            $xml = $this->createXML($pfObj, 'list', $prop);
            $xmlContent = $xml->saveXML();

            // Save XML content to file
            $filePath = storage_path('app/public/xml/propertyFinder.xml');
            file_put_contents($filePath, $xmlContent);

            return response()->json(['message' => 'XML file generated successfully', 'file_path' => $filePath], 200);
        } catch (\Exception $e) {
            info($e);
            return response()->json(['error' => 'Failed to generate XML file', 'msg' => $e->getMessage()], 500);
        }
    }

    public function getPropertyFinderXMLDataFromFile(Request $request)
    {
        // info() name of file and time
        info('PropertyFinder is called at ' . now());

        $mainPortal = Portal::where("slug", "property-finder")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        abort_if($mainPortal->xml_url != $request->key, 404);

        try {
            $filePath = storage_path('app/public/xml/propertyFinder.xml');
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'XML file not found'], 404);
            }

            $xmlContent = file_get_contents($filePath);
            return response($xmlContent, 200)->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve XML data'], 500);
        }
    }

    public function generatePropertyFinderAbuDhabiXMLFile()
    {
        try {
            $mainPortal = Portal::where("slug", "property-finder")->latest()->first();
            if (is_null($mainPortal)) {
                abort(404);
            }
            // abort_if($mainPortal->xml_url != $request->key, 404);

            $prop = [];
            $completionStatusMapping = [
                'Ready Primary' => 'completed_primary',
                'Ready Secondary' => 'completed',
                'Off-Plan Primary' => 'off_plan_primary',
                'Off-Plan Secondary' => 'off_plan',
            ];

            $furnishedMapping = [
                'FURNISHED' => 'Yes',
                'SEMI_FURNISHED' => 'Partly',
            ];

            $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
                ->where(['is_live' => true])
                ->where('completion_status', '!=', 'Off-Plan Primary')
                ->whereHas('community.region', function ($q) {
                    $q->where('name', "Abu Dhabi");
                })
                ->with([
                    'agent' => function ($q) {
                        $q->select('id', 'name', 'first_name', 'last_name', 'pp_enabled', 'pp_name', 'pp_photo', 'pp_license_no', 'email', 'secondary_email', 'license_no', 'mobile', 'bio', 'thumbnail', 'image');
                    },
                    'details',
                    'propertyCategory' => function ($q) {
                        $q->select('id');
                    },
                    'propertyCategory.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'title', 'property_category_id', 'portal_id');
                    },
                    'bed' => function ($q) {
                        $q->select('id');
                    },
                    'bed.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                    }, 'developer',
                    'price' => function ($q) {
                        $q->select('id', 'property_id', 'price', 'prices', 'price_on_application');
                    },
                    'bath' => function ($q) {
                        $q->select('id');
                    },
                    'bath.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                    },
                    'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function ($q) use ($mainPortal) {
                            $q->where('portal_id', '=', $mainPortal->id);
                        });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                            ->where('title', '!=', '');
                    },
                    'community.region', 'subCommunity', 'communityProperty',
                    'photos' => function ($q) {
                        $q->select('id', 'property_id', 'path', 'title', 'updated_at');
                    },
                    'floorPlan'])
                ->orderBy('properties.created_at', 'desc')
                ->get();
            $listing_count = $pp->count();
            $mostRecentListing = $pp->sortByDesc('updated_at')->first();
            foreach ($pp as $key => $value) {
                $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($mostRecentListing->updated_at))];
                $data['reference_number'] = $value['ref_no'];
                $data['permit_number'] = $value['rera_permit_number'] ?? '';
                $data['price'] = [];
                if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                    $data['price'] = $value['price']["price"] ?? 0;
                    $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
                } else {
                    $data['price']['yearly'] = $value['price']["price"] ?? 0;
                    $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
                    $data['cheques'] = $value['cheques'] ?? null;
                }
                $data['property_type'] = $value['propertyCategory']['portals'][0]["title"] ?? "";
                $data['price_on_application'] = $value['price']['price_on_application'] ? 'Yes' : 'No';

                $data['availability_date'] = $value?->availability_date;
                // $data['title_deed']['@cdata'] = $value?->title_deed_text ?? '';
                $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
                $data['community']['@cdata'] = $value['community']['name'] ?? '';
                $data['sub_community']['@cdata'] = $value['subCommunity']['name'] ?? '';

                if (($value?->communityProperty?->name ?? null) && (!$value?->communityProperty?->default_use)) {
                    $data['property_name']['@cdata'] = '';
                } else {
                    $data['property_name']['@cdata'] = $value['communityProperty']['name'] ?? '';
                }

                $data['title_en']['@cdata'] = $value['title'] ?? '';
                //$data['description_en']['@cdata'] = $value['description'] ?? '';

                $description = strip_tags($value['description'], '<br>');
                $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $description) ?? '';

                $data['completion_status'] = $completionStatusMapping[$value['completion_status']] ?? 'completed';
                $data['floor'] = $value['floor'] ?? '';
                $data['parking'] = $value['parking'] ?? 0;
                $data['furnished'] = $furnishedMapping[$value['furnished']] ?? 'No';
                $data['size'] = $value['built_up_area'] ?? '';
                $data['plot_size'] = $value['plot_size'] ?? '';
                $data['build_year'] = $value['build_year'] ?? '';

                $data['payment_method'] = $value['payment_method'] ?? '';

                if ($value['payment_method'] == "installments" || $value['payment_method'] == "cash,installments") {
                    $data['down_payment_price'] = $value['down_payment_price'] ?? '';
                }
                $data['bedroom'] = $value["bed"]["portals"][0]["name"] ?? 0;
                $data['bathroom'] = $value["bath"]["portals"][0]["name"] ?? 0;
                $data['view'] = $value['view'] ?? '';
                $privateAmenities = [];
                $commercialAmenities = [];
                $features = [];

                foreach ($value["amenities"] ?? [] as $amenity) {
                    $amenityType = $amenity["amenity"]["amenity_type"] ?? [];
                    if ($amenityType == "private") {
                        $privateAmenities[] = $amenity["amenity"]["portals"][0]['title'];
                    }
                    if ($amenityType == "commercial") {
                        $commercialAmenities[] = $amenity["amenity"]["portals"][0]['title'];
                    }
                    $features[] = $amenity["amenity"]["portals"][0]["title"];
                }
                $data['private_amenities'] = implode(",", $privateAmenities);
                $data['commercial_amenities'] = implode(",", $commercialAmenities);
                $data['amenities'] = implode(',', $features);

                //$data['view'] = $value['view'] ?? '';

                if ($value['agent']) {
                    $data['agent']['id'] = $value['agent']['id'];

                    if ($value['agent']['pp_enabled']) {
                        $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                        $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                        $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? '';
                    } else {
                        $data['agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                        $data['agent']['photo'] = $value['agent']['image_url'] ?? '';
                        $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                    }
                    if ($value['agent']['secondary_email'] != "") {
                        $data['agent']['email'] = $value['agent']['secondary_email'] ?? '';
                    } else {
                        $data['agent']['email'] = $value['agent']['email'] ?? '';
                    }
                    $number = str_replace("+971", "971", $value['agent']['mobile']) ?? '';
                    $data['agent']['phone'] = str_replace(' ', '', $number);
                    $data['agent']['info'] = $value['agent']['bio'] ?? '';

                }
                $data['developer'] = $value['developer']['short_name'] ?? $value['details']['developer'] ?? null;
                $data['photo'] = [];
                if ($value['photos']) {
                    $url = [];
                    foreach ($value['photos'] as $key => $photo) {
                        $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s', strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                        $dd['@value'] = $photo['path'];
                        $url['url'][] = $dd;
                    }
                    $data['photo'] = $url;
                }

                $data['video_tour_url'] = $value['video_link'] ?? '';
                $data['view360'] = $value['video_360_link'] ?? '';

                $data['floor_plan'] = [];

                if ($value['floor_plan']) {
                    $url = [];
                    $dd['@attributes'] = ['last_updated' => $value['floor_plan']['updated_at'], 'watermark' => 'no'];
                    $dd['@value'] = $value['floor_plan']['path'] ?? null;
                    $url['url'][] = $dd;
                    $data['floor_plan'] = $url;
                }

                $prop['property'][] = $data;
                if (!empty($value["communityProperty"])) {
                    $data['geopoints'] = $value["communityProperty"]["latitude"] . ", " . $value["communityProperty"]["longitude"];
                } elseif (!empty($value["subCommunity"])) {
                    $data['geopoints'] = $value["subCommunity"]["latitude"] . ", " . $value["subCommunity"]["longitude"];
                } else {
                    $data['geopoints'] = ''; //$value['latitude'] . ',' . $value['longitude'];
                }
            }

            $last_update = $mostRecentListing->updated_at;
            $this->listing_count = $listing_count;
            $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update));
            $pfObj['listing_count'] = $listing_count;

            $xml = $this->createXML($pfObj, 'list', $prop);
            $xmlContent = $xml->saveXML();

            // Save XML content to file
            $filePath = storage_path('app/public/xml/propertyFinderAbuDhabi.xml');
            file_put_contents($filePath, $xmlContent);

            return response()->json(['message' => 'XML file generated successfully', 'file_path' => $filePath], 200);
        } catch (\Exception $e) {
            info($e);
            return response()->json(['error' => 'Failed to generate XML file', 'msg' => $e->getMessage()], 500);
        }
    }

    public function getPropertyFinderAbuDhabiXMLDataFromFile(Request $request)
    {
        info('PropertyFinder Abu Dhabi is called at ' . now());

        try {
            $filePath = storage_path('app/public/xml/propertyFinderAbuDhabi.xml');
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'XML file not found'], 404);
            }

            $xmlContent = file_get_contents($filePath);
            return response($xmlContent, 200)->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve XML data'], 500);
        }
    }

    public function generatePropertyFinderOffPlanXMLFile()
    {
        try {
            $mainPortal = Portal::where("slug", "property-finder")->latest()->first();
            if (is_null($mainPortal)) {
                abort(404);
            }
            // abort_if($mainPortal->xml_url != $request->key, 404);
            // dd($mainPortal);
            $prop = [];

            $completionStatusMapping = [
                'Ready Primary' => 'completed_primary',
                'Ready Secondary' => 'completed',
                'Off-Plan Primary' => 'off_plan_primary',
                'Off-Plan Secondary' => 'off_plan',
            ];

            $furnishedMapping = [
                'FURNISHED' => 'Yes',
                'SEMI_FURNISHED' => 'Partly',
            ];

            $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
                ->where(['is_live' => true])
                ->where('completion_status', 'Off-Plan Primary')
                ->where('agent_id', 4) // Dimitry Zolotoco
                ->with([
                    'agent' => function ($q) {
                        $q->select('id', 'name', 'first_name', 'last_name', 'pp_enabled', 'pp_name', 'pp_photo', 'pp_license_no', 'email', 'secondary_email', 'license_no', 'mobile', 'bio', 'thumbnail', 'image');
                    },
                    'details',
                    'propertyCategory' => function ($q) {
                        $q->select('id');
                    },
                    'propertyCategory.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'title', 'property_category_id', 'portal_id');
                    },
                    'bed' => function ($q) {
                        $q->select('id');
                    },
                    'bed.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                    },
                    'developer',
                    'price' => function ($q) {
                        $q->select('id', 'property_id', 'price', 'prices', 'price_on_application');
                    },
                    'bath' => function ($q) {
                        $q->select('id');
                    },
                    'bath.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                    },
                    'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function ($q) use ($mainPortal) {
                            $q->where('portal_id', '=', $mainPortal->id);
                        });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)->where('title', '!=', '');
                    },
                    'community.region', 'subCommunity', 'communityProperty',
                    'photos' => function ($q) {
                        $q->select('id', 'property_id', 'path', 'title', 'updated_at');
                    },
                    'floorPlan',
                ])
                ->orderBy('properties.created_at', 'desc')
                ->get();

            $listing_count = $pp->count();
            if ($listing_count == 0) {
                info('No listings found for Property Finder Off Plan XML V2');
                return response()->json(['error' => 'No listings found'], 404);
            }
            $mostRecentListing = $pp->sortByDesc('updated_at')->first();

            if ($listing_count > 0) {

                foreach ($pp as $key => $value) {
                    $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($mostRecentListing->updated_at))];
                    $data['reference_number'] = $value['ref_no'];
                    $data['permit_number'] = $value['rera_permit_number'] ?? '';
                    $data['price'] = [];
                    if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                        $data['price'] = $value['price']["price"] ?? 0;
                        $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
                    } else {
                        $data['price']['yearly'] = $value['price']["price"] ?? 0;
                        $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
                        $data['cheques'] = $value['cheques'] ?? null;
                    }
                    $data['property_type'] = $value['propertyCategory']['portals'][0]["title"] ?? "";
                    $data['price_on_application'] = $value['price']['price_on_application'] ? 'Yes' : 'No';

                    $data['availability_date'] = $value->availability_date;
                    $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
                    $data['community']['@cdata'] = $value['community']['name'] ?? '';
                    $data['sub_community']['@cdata'] = $value['subCommunity']['name'] ?? '';

                    if (($value->communityProperty->name ?? null) && (!$value->communityProperty->default_use)) {
                        $data['property_name']['@cdata'] = '';
                    } else {
                        $data['property_name']['@cdata'] = $value['communityProperty']['name'] ?? '';
                    }

                    $data['title_en']['@cdata'] = $value['title'] ?? '';
                    $description = strip_tags($value['description'], '<br>');
                    $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $description) ?? '';

                    $data['completion_status'] = $completionStatusMapping[$value['completion_status']] ?? 'completed';
                    $data['floor'] = $value['floor'] ?? '';
                    $data['parking'] = $value['parking'] ?? 0;
                    $data['furnished'] = $furnishedMapping[$value['furnished']] ?? 'No';
                    $data['size'] = $value['built_up_area'] ?? '';
                    $data['plot_size'] = $value['plot_size'] ?? '';
                    $data['build_year'] = $value['build_year'] ?? '';

                    $data['payment_method'] = $value['payment_method'] ?? '';
                    if ($value['payment_method'] == "installments" || $value['payment_method'] == "cash,installments") {
                        $data['down_payment_price'] = $value['down_payment_price'] ?? '';
                    }
                    $data['bedroom'] = $value["bed"]["portals"][0]["name"] ?? 0;
                    $data['bathroom'] = $value["bath"]["portals"][0]["name"] ?? 0;
                    $data['view'] = $value['view'] ?? '';

                    $privateAmenities = [];
                    $commercialAmenities = [];
                    $features = [];

                    foreach ($value["amenities"] ?? [] as $amenity) {
                        $amenityType = $amenity["amenity"]["amenity_type"] ?? [];
                        if ($amenityType == "private") {
                            $privateAmenities[] = $amenity["amenity"]["portals"][0]['title'];
                        }
                        if ($amenityType == "commercial") {
                            $commercialAmenities[] = $amenity["amenity"]["portals"][0]['title'];
                        }
                        $features[] = $amenity["amenity"]["portals"][0]["title"];
                    }
                    $data['private_amenities'] = implode(",", $privateAmenities);
                    $data['commercial_amenities'] = implode(",", $commercialAmenities);
                    $data['amenities'] = implode(',', $features);

                    if ($value['agent']) {
                        $data['agent']['id'] = $value['agent']['id'];
                        if ($value['agent']['pp_enabled']) {
                            $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                            $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                            $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? '';
                        } else {
                            $data['agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                            $data['agent']['photo'] = $value['agent']['image_url'] ?? '';
                            $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                        }
                        if ($value['agent']['secondary_email'] != "") {
                            $data['agent']['email'] = $value['agent']['secondary_email'] ?? '';
                        } else {
                            $data['agent']['email'] = $value['agent']['email'] ?? '';
                        }
                        $number = str_replace("+971", "971", $value['agent']['mobile']) ?? '';
                        $data['agent']['phone'] = str_replace(' ', '', $number);
                        $data['agent']['info'] = $value['agent']['bio'] ?? '';
                    }

                    $data['developer'] = $value['developer']['short_name'] ?? $value['details']['developer'] ?? null;
                    $data['photo'] = [];
                    if ($value['photos']) {
                        $url = [];
                        foreach ($value['photos'] as $key => $photo) {
                            $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s', strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                            $dd['@value'] = $photo['path'];
                            $url['url'][] = $dd;
                        }
                        $data['photo'] = $url;
                    }

                    $data['video_tour_url'] = $value['video_link'] ?? '';
                    $data['view360'] = $value['video_360_link'] ?? '';

                    $data['floor_plan'] = [];
                    if ($value['floor_plan']) {
                        $url = [];
                        $dd['@attributes'] = ['last_updated' => $value['floor_plan']['updated_at'], 'watermark' => 'no'];
                        $dd['@value'] = $value['floor_plan']['path'] ?? null;
                        $url['url'][] = $dd;
                        $data['floor_plan'] = $url;
                    }

                    $prop['property'][] = $data;
                    if (!empty($value["communityProperty"])) {
                        $data['geopoints'] = $value["communityProperty"]["latitude"] . ", " . $value["communityProperty"]["longitude"];
                    } elseif (!empty($value["subCommunity"])) {
                        $data['geopoints'] = $value["subCommunity"]["latitude"] . ", " . $value["subCommunity"]["longitude"];
                    } else {
                        $data['geopoints'] = '';
                    }
                }

                $last_update = $mostRecentListing->updated_at;
                $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update));
                $pfObj['listing_count'] = $listing_count;

                $xml = $this->createXML($pfObj, 'list', $prop);
                $xmlContent = $xml->saveXML();

                // Save XML content to file
                $filePath = storage_path('app/public/xml/propertyFinderOffPlan.xml');
                file_put_contents($filePath, $xmlContent);

                return response()->json(['message' => 'XML file generated successfully', 'file_path' => $filePath], 200);
            }
            return response()->json(['message' => 'No data found'], 200);
        } catch (\Exception $e) {
            info($e);
            return response()->json(['error' => 'Failed to generate XML file', 'msg' => $e->getMessage()], 500);
        }
    }

    public function getPropertyFinderOffPlanXMLDataFromFile(Request $request)
    {
        info('PropertyFinder Offplan is called at ' . now());

        try {
            $filePath = storage_path('app/public/xml/propertyFinderOffPlan.xml');
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'XML file not found'], 404);
            }

            $xmlContent = file_get_contents($filePath);
            return response($xmlContent, 200)->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve XML data'], 500);
        }
    }

    public function generateDubizzleXMLFile()
    {
        try {
            $mainPortal = Portal::where("name", "Dubizzle")->latest()->first();
            if (is_null($mainPortal)) {
                abort(404);
            }
            // abort_if($mainPortal->xml_url != $request->key, 404);

            $prop = [];

            $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
                ->where(['is_live' => true])
                ->with([
                    'agent' => function ($q) {
                        $q->select('id', 'name', 'first_name', 'last_name', 'pp_enabled', 'pp_name', 'pp_photo', 'pp_license_no', 'email', 'secondary_email', 'license_no', 'mobile', 'bio', 'thumbnail', 'image');
                    },
                    'details',
                    'propertyCategory' => function ($q) {
                        $q->select('id');
                    },
                    'propertyCategory.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'title', 'property_category_id', 'portal_id');
                    },
                    'bed' => function ($q) {
                        $q->select('id');
                    },
                    'bed.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                    }, 'developer',
                    'price' => function ($q) {
                        $q->select('id', 'property_id', 'price', 'prices', 'price_on_application');
                    },
                    'bath' => function ($q) {
                        $q->select('id');
                    },
                    'bath.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                    },
                    'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function ($q) use ($mainPortal) {
                            $q->where('portal_id', '=', $mainPortal->id);
                        });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                            ->where('title', '!=', '');
                    },
                    'community.region', 'subCommunity', 'communityProperty',
                    'photos' => function ($q) {
                        $q->select('id', 'property_id', 'path', 'title', 'updated_at');
                    },
                    'floorPlan'])
                ->orderBy('properties.created_at', 'desc')
                ->get()
                ->toArray();

            foreach ($pp as $key => $value) {
                if ($value['publish_status'] == 'UNPUBLISHED') {
                    $data['status'] = 'deleted';
                } else {
                    $data['status'] = 'vacant';
                }

                if ($value['for'] == 'Residential for sale' || $value['for'] == 'Commercial for sale') {
                    $data['type'] = 'SP';
                } else {
                    $data['type'] = 'RP';
                }

                $data['subtype'] = $value['property_category']['portals'][0]['title'] ?? "";

                $data['commercialtype'] = 'IN';
                $data['refno'] = $value['ref_no'];
                $data['title']['@cdata'] = $value['title'] ?? '';
                $data['description']['@cdata'] = strip_tags($value['description']) ?? '';
                $data['size'] = $value['built_up_area'] ?? '';
                $data['sizeunits'] = 'SqFt';
                $data['price'] = $value['price'] ? $value['price']['price'] : 0;
                $data['locationtext']['@cdata'] = $value['community']['name'] ?? '';
                if ($value['for'] == 'Residential for sale' || $value['for'] == 'Commercial for sale') {
                    $data['rentispaid'] = '';
                } else {
                    $data['rentispaid'] = 'YR';
                }

                $data['furnished'] = $value['furnished'] == 'FURNISHED' ? 1 : 0;
                $data['building']['@cdata'] = $value['community_property']['name'] ?? ($value['sub_community']['name'] ?? "");
                $data['city'] = 2;

                if ($value['for'] == 'Residential for rent' || $value['for'] == 'Commercial for rent') {
                    $data['rentpriceterm'] = 'YR';
                }
                $data['pricecurrency'] = 'AED';
                $data['lastupdated'] = $value['updated_at'];

                $data['bedrooms'] = $value["bed"]["portals"][0]["name"] ?? 0;
                $data['bathrooms'] = $value["bath"]["portals"][0]["name"] ?? 0;

                $privateAmenities = [];
                $commercialAmenities = [];
                foreach ($value["amenities"] ?? [] as $amenity) {
                    $amenityType = $amenity["amenity"]["amenity_type"] ?? [];
                    if ($amenityType == "private") {
                        $privateAmenities[] = $amenity["amenity"]["portals"][0]['title'];
                    }
                    if ($amenityType == "commercial") {
                        $commercialAmenities[] = $amenity["amenity"]["portals"][0]['title'];
                    }
                }
                $data['privateamenities'] = implode(",", $privateAmenities);
                $data['commercialamenities'] = implode(",", $commercialAmenities);

                if (!empty($value["community_property"])) {
                    $data['geopoint'] = $value["community_property"]["latitude"] . ", " . $value["community_property"]["longitude"];
                } elseif (!empty($value["sub_community"])) {
                    $data['geopoint'] = $value["sub_community"]["latitude"] . ", " . $value["sub_community"]["longitude"];
                } else {
                    $data['geopoint'] = '';
                }

                if ($value['agent']) {
                    $data['contactemail'] = $value['agent']['email'];
                    $data['contactnumber'] = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
                }

                $data['photos'] = [];
                if ($value['photos']) {
                    $url = '';
                    foreach ($value['photos'] as $key => $photo) {
                        $url .= $photo['path'] . '|';
                    }

                    $data['photos'] = substr($url, 0, -1);
                }
                $data['video_url'] = $value['link_youtube'] ?? '';
                $data['view360'] = $value['video_360_link'] ?? '';

                $data['price_on_application'] = 'No';
                $data['permit_number'] = $value['rera_permit_number'] ?? '';

                $prop['property'][] = $data;
            }

            $pfObj = [];

            $xml = $this->createXML($pfObj, 'dubizzlepropertyfeed', $prop);
            $xmlContent = $xml->saveXML();

            // Save XML content to file
            $filePath = storage_path('app/public/xml/dubizzle.xml');
            file_put_contents($filePath, $xmlContent);

            return response()->json(['message' => 'XML file generated successfully', 'file_path' => $filePath], 200);
        } catch (\Exception $e) {
            info($e);
            return response()->json(['error' => 'Failed to generate XML file', 'msg' => $e->getMessage()], 500);
        }
    }

    public function getDubizzleXMLDataFromFile(Request $request)
    {
        try {
            $filePath = storage_path('app/public/xml/dubizzle.xml');
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'XML file not found'], 404);
            }

            $xmlContent = file_get_contents($filePath);
            return response($xmlContent, 200)->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve XML data'], 500);
        }
    }

    public function generateBayutXMLFile()
    {
        try {
            $mainPortal = Portal::where("name", "Bayut")->latest()->first();
            if (is_null($mainPortal)) {
                abort(404);
            }
            // abort_if('yxBIL7TZ3UMbaiLq' != $request->key, 404);

            $prop = [];

            $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
                ->where(['is_live' => true])
                ->with([
                    'agent' => function ($q) {
                        $q->select('id', 'name', 'first_name', 'last_name', 'pp_enabled', 'pp_name', 'pp_photo', 'pp_license_no', 'email', 'secondary_email', 'license_no', 'mobile', 'bio', 'thumbnail', 'image');
                    },
                    'details',
                    'propertyCategory' => function ($q) {
                        $q->select('id');
                    },
                    'propertyCategory.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'title', 'property_category_id', 'portal_id');
                    },
                    'bed' => function ($q) {
                        $q->select('id');
                    },
                    'bed.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                    }, 'developer',
                    'price' => function ($q) {
                        $q->select('id', 'property_id', 'price', 'prices', 'price_on_application');
                    },
                    'bath' => function ($q) {
                        $q->select('id');
                    },
                    'bath.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                    },
                    'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function ($q) use ($mainPortal) {
                            $q->where('portal_id', '=', $mainPortal->id);
                        });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                            ->where('title', '!=', '');
                    },
                    'community.region', 'subCommunity', 'communityProperty',
                    'photos' => function ($q) {
                        $q->select('id', 'property_id', 'path', 'title', 'updated_at');
                    },
                    'floorPlan'])
                ->orderBy('properties.created_at', 'desc')
                ->get()
                ->toArray();

            foreach ($pp as $key => $value) {
                $data['Property_Ref_No'] = $value['ref_no'];

                $data['Rent_Frequency']['@cdata'] = '';
                if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                    $data['Property_purpose']['@cdata'] = 'Buy';
                } else {
                    $data['Property_purpose']['@cdata'] = 'Rent';
                    $data['Rent_Frequency']['@cdata'] = 'yearly';
                }

                $data['Property_Type']['@cdata'] = $value['property_category']['portals'][0]['title'] ?? "";
                $data['Property_Status']['@cdata'] = 'live';

                $data['City']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
                $data['Locality']['@cdata'] = $value['community']['name'] ?? '';
                $data['Sub_Locality']['@cdata'] = $value['sub_community']['name'];
                $data['Tower_Name']['@cdata'] = $value['community_property']['name'] ?? '';
                $data['Property_Title']['@cdata'] = $value['title'] ?? '';
                $data['Property_Description']['@cdata'] = $value['description'] ?? '';
                if ($value['furnished'] == 'FURNISHED') {
                    $data['Furnished']['@cdata'] = 'Yes';
                } else {
                    if ($value['furnished'] == 'SEMI_FURNISHED') {
                        $data['Furnished']['@cdata'] = 'Partly';
                    } else {
                        $data['Furnished']['@cdata'] = 'No';
                    }
                }

                $data['Property_Size']['@cdata'] = $value['built_up_area'] . '' ?? '';
                $data['Property_Size_Unit']['@cdata'] = "SQFT";

                $data['bedroom'] = $value["bed"]["portals"][0]["name"] ?? 0;
                $data['bathroom'] = $value["bath"]["portals"][0]["name"] ?? 0;

                $data['Price']['@cdata'] = ($value['price']['price'] ?? '') . '';
                if ($value['agent']) {
                    $data['Listing_Agent']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                    if ($value['agent']['email'] == $value['agent']['email']) {
                        $data['Listing_Agent_Email']['@cdata'] = $value['agent']['email'];
                    } else {
                        $data['Listing_Agent_Email']['@cdata'] = $value['agent']['email'];
                    }
                    $data['Listing_Agent_Phone']['@cdata'] = $value['agent']['mobile'];
                }

                foreach ($value["amenities"] ?? [] as $amenity) {
                    $data['Features']['Feature'][]['@cdata'] = $amenity["amenity"]["portals"][0]['title'];
                }

                $data['Images'] = [];

                if ($value['photos']) {
                    foreach ($value['photos'] as $key => $photo) {
                        $data['Images']['Image'][]['@cdata'] = $photo['path'];
                    }
                }

                $data['Videos']['Video']['@cdata'] = $value['video_link'];
                $data['Last_Updated']['@cdata'] = date('Y-m-d H:i:s', strtotime($value['updated_at']));
                $data['Permit_Number']['@cdata'] = $value['rera_permit_number'] ?? '';

                if ($value['completion_status'] == 'Off-Plan Primary' || $value['completion_status'] == 'Off-Plan Secondary') {
                    $data['Off_Plan']['@cdata'] = 'Yes';
                } else {
                    $data['Off_Plan']['@cdata'] = 'No';
                }

                $prop['Property'][] = $data;
            } // end foreach

            $xml = $this->createXML([], 'Properties', $prop);
            $xmlContent = $xml->saveXML();

            // Save XML content to file
            $filePath = storage_path('app/public/xml/bayut.xml');
            file_put_contents($filePath, $xmlContent);

            return response()->json(['message' => 'XML file generated successfully', 'file_path' => $filePath], 200);
        } catch (\Exception $e) {
            info($e);
            return response()->json(['error' => 'Failed to generate XML file', 'msg' => $e->getMessage()], 500);
        }
    }

    public function getBayutXMLDataFromFile(Request $request)
    {
        try {
            $filePath = storage_path('app/public/xml/bayut.xml');
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'XML file not found'], 404);
            }

            $xmlContent = file_get_contents($filePath);
            return response($xmlContent, 200)->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve XML data'], 500);
        }
    }
    public function generateBayutDubizzleAbuDhabiXMLFile()
    {

        try {
            $mainPortal = Portal::where("name", "Bayut")->latest()->first();
            if (is_null($mainPortal)) {
                abort(404);
            }
            // abort_if($mainPortal->xml_url != $request->key, 404);

            $prop = [];

            $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id)->orWhere('portal_id', 3);
            })
                ->where(['is_live' => true])
                ->whereHas('community.region', function ($q) {
                    $q->where('name', 'Abu Dhabi');
                })
                ->with([
                    'agent' => function ($q) {
                        $q->select('id', 'name', 'first_name', 'last_name', 'pp_enabled', 'pp_name', 'pp_photo', 'pp_license_no', 'email', 'secondary_email', 'license_no', 'mobile', 'bio', 'thumbnail', 'image');
                    },
                    'details',
                    'propertyCategory' => function ($q) {
                        $q->select('id');
                    },
                    'propertyCategory.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'title', 'property_category_id', 'portal_id');
                    },
                    'bed' => function ($q) {
                        $q->select('id');
                    },
                    'bed.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                    }, 'developer',
                    'price' => function ($q) {
                        $q->select('id', 'property_id', 'price', 'prices', 'price_on_application');
                    },
                    'bath' => function ($q) {
                        $q->select('id');
                    },
                    'bath.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                    },
                    'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function ($q) use ($mainPortal) {
                            $q->where('portal_id', '=', $mainPortal->id);
                        });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                            ->where('title', '!=', '');
                    },
                    'community.region', 'subCommunity', 'communityProperty', 'portals',
                    'photos' => function ($q) {
                        $q->select('id', 'property_id', 'path', 'title', 'updated_at');
                    },
                    'floorPlan'])
                ->orderBy('properties.created_at', 'desc')
                ->get();

            foreach ($pp as $key => $value) {
                $data['Property_Ref_No'] = $value?->ref_no;

                $data['Rent_Frequency']['@cdata'] = '';
                if ($value?->for == "Residential for sale" || $value?->for == "Commercial for sale") {
                    $data['Property_purpose']['@cdata'] = 'Buy';
                } else {
                    $data['Property_purpose']['@cdata'] = 'Rent';
                    $data['Rent_Frequency']['@cdata'] = 'yearly';
                }
                $data['Property_Type']['@cdata'] = $value?->propertyCategory?->portals?->first()?->title ?? '';
                //$data['Property_Type']['@cdata'] = $value['property_category']['portals'][0]['title'] ?? '';
                $data['Property_Status']['@cdata'] = 'live';

                $data['City']['@cdata'] = $value?->community?->region?->name ?? 'Dubai';
                $data['Locality']['@cdata'] = $value?->community?->alternate_name ?? $value?->community?->name ?? '';
                $data['Sub_Locality']['@cdata'] = $value?->subCommunity?->alternate_name ?? $value?->subCommunity?->name ?? '';
                if (($value?->communityProperty?->alternate_name ?? null) && (!$value?->communityProperty?->alternate_use)) {
                    $data['Tower_Name']['@cdata'] = '';
                } else {
                    $data['Tower_Name']['@cdata'] = $value?->communityProperty?->alternate_name ?? $value?->communityProperty?->name ?? '';
                }
                $data['Property_Title']['@cdata'] = $value->title ?? '';
                //$data['Property_Description']['@cdata'] = $value['description'] ?? '';
                $description = strip_tags($value->description, '<br>');
                $updateDescription = str_replace("<br />", "\r\n", $description);
                $data['Property_Description']['@cdata'] = str_replace("<br>", "\r\n", $updateDescription) ?? '';
                if ($value->furnished == 'FURNISHED') {
                    $data['Furnished']['@cdata'] = 'Yes';
                } else {
                    if ($value['furnished'] == 'SEMI_FURNISHED') {
                        $data['Furnished']['@cdata'] = 'Partly';
                    } else {
                        $data['Furnished']['@cdata'] = 'No';
                    }
                }

                $data['Property_Size']['@cdata'] = str_replace(',', '', $value['built_up_area'] . '') ?? '';
                $data['Property_Size_Unit']['@cdata'] = "SQFT";

                $data['bedroom'] = $value?->bed?->portals?->first()?->name ?? 0;
                $data['bathroom'] = $value?->bath?->portals->first()->name ?? 0;

                $data['Price']['@cdata'] = ($value?->price?->price ?? '') . '';
                if ($value['agent']) {
                    $data['Listing_Agent']['@cdata'] = $value?->agent?->first_name . ' ' . $value?->agent?->last_name;

                    $data['Listing_Agent_Email']['@cdata'] = $value?->agent?->secondary_email != "" ? $value?->agent?->secondary_email : $value?->agent?->email;
                    if ($value['agent']['pp_enabled']) {
                        $data['Listing_Agent']['@cdata'] = $value?->agent?->pp_name;
                    }
                    $data['Listing_Agent_Phone']['@cdata'] = $value?->agent?->mobile;
                }
                $data['Features']['Feature'] = array();
                foreach ($value["amenities"] ?? [] as $amenity) {

                    $data['Features']['Feature'][]['@cdata'] = $amenity?->amenity?->portals?->first()?->title;
                    // foreach ($amenity["amenity"]["portals"] ?? [] as $portal) {
                    //     if ($portal["portal_id"] == $mainPortal->id && !empty($portal["title"])) {
                    //         $data['Features']['Feature'][]['@cdata'] = $portal["title"];
                    //     }
                    // }
                }

                $data['Images'] = [];

                if ($value['photos']) {
                    foreach ($value->photos ?? [] as $key => $photo) {
                        $data['Images']['Image'][]['@cdata'] = $photo?->path;
                    }
                }

                $data['Videos']['Video']['@cdata'] = $value?->video_link;
                $data['Last_Updated']['@cdata'] = date('Y-m-d H:i:s', strtotime($value?->updated_at));
                $data['Permit_Number']['@cdata'] = $value['rera_permit_number'] ?? '';

                if ($value['completion_status'] == 'Off-Plan Primary' || $value?->completion_status == 'Off-Plan Secondary') {
                    $data['Off_Plan']['@cdata'] = 'Yes';
                } else {
                    $data['Off_Plan']['@cdata'] = 'No';
                }
                $data['Portals'] = [];

                foreach ($value['portals'] as $portal) {
                    if ($portal['portal_id'] == 3) {
                        $data['Portals']['Portal'][]['@cdata'] = 'Dubizzle';
                    }
                    if ($portal['portal_id'] == 2) {
                        $data['Portals']['Portal'][]['@cdata'] = 'Bayut';
                    }
                }

                $prop['Property'][] = $data;
            } // end foreach

            $xml = $this->createXML([], 'Properties', $prop);
            $xmlContent = $xml->saveXML();

            // Save XML content to file
            $filePath = storage_path('app/public/xml/bayut_dubizzle_abu_dhabi.xml');
            file_put_contents($filePath, $xmlContent);

            return response()->json(['message' => 'XML file generated successfully', 'file_path' => $filePath], 200);
        } catch (\Exception $e) {
            info($e);
            return response()->json(['error' => 'Failed to generate XML file', 'msg' => $e->getMessage()], 500);
        }
    }

    public function getBayutDubizzleAbuDhabiXMLDataFromFile(Request $request)
    {

        try {
            $filePath = storage_path('app/public/xml/bayut_dubizzle_abu_dhabi.xml');
            if (!file_exists($filePath)) {
                abort(404, 'XML file not found');
            }

            if($request->key != 'yxBIL7TZ3UMbaiLq'){
                abort(404, 'Invalid key');
            }


            $xmlContent = file_get_contents($filePath);
            $chunkSize = 500;

            return response()->stream(function () use ($xmlContent, $chunkSize) {
                $length = strlen($xmlContent);
                $start = 0;

                while ($start < $length) {
                    $chunk = substr($xmlContent, $start, $chunkSize);
                    $start += $chunkSize;
                    echo $chunk;
                    flush();
                }
            }, 200, [
                'Content-Type' => 'text/xml',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve XML data'], 500);
        }
    }

    public function generatePrivateSiteXMLFile()
    {
        try {
            $mainPortal = Portal::where("slug", "private-site")->latest()->first();
            if (is_null($mainPortal)) {
                abort(404);
            }

            $last_update = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
                ->where(['is_live' => true])
                ->orderBy('updated_at', 'desc')
                ->select('updated_at')->first()->toArray();

            $listing_count = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
                ->where(['is_live' => true])
                ->count();

            $prop = [];

            $pp = Property::where('publish_status', 'PUBLISHED')->whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })->with(
                'details',
                'agent',
                'propertyCategory.portals',
                'bed.portals',
                'price',
                'bath.portals',
                'amenities.amenity.portals',
                'community.region',
                'subCommunity',
                'communityProperty',
                'photos',
                'floorPlan'
            )
                ->orderBy('properties.created_at', 'desc')
                ->get()
                ->toArray();

            foreach ($pp as $key => $value) {
                $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($value['updated_at']))];
                $data['permit_number'] = $value['rera_permit_number'] ?? '';
                $data['reference_number'] = $value['ref_no'];
                $data['price'] = [];
                if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                    $data['price'] = $value['price']["price"] ?? 0;
                    $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
                } else {
                    $data['price']['yearly'] = $value['price']["price"] ?? 0;
                    $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
                }
                $data['property_type'] = collect($value['property_category']['portals'] ?? [])->where(
                    "portal_id",
                    $mainPortal->id
                )->first()["title"] ?? "";

                $data['price_on_application'] = 'No';

                $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
                $data['community']['@cdata'] = $value['community']['name'] ?? '';
                $data['sub_community']['@cdata'] = $value['sub_community']['name'] ?? '';
                $data['property_name']['@cdata'] = $value['community_property']['name'] ?? '';

                $data['title_en']['@cdata'] = $value['title'] ?? '';
                $data['description_en']['@cdata'] = $value['description'] ?? '';
                $data['completion_status'] = $value['completion_status'] ?? "";

                $features = [];

                foreach ($value["amenities"] ?? [] as $amenity) {
                    $amenityType = $amenity["amenity"]["amenity_type"] ?? [];
                    foreach ($amenity["amenity"]["portals"] ?? [] as $portal) {
                        if ($portal["portal_id"] == $mainPortal->id && !empty($portal["title"])) {
                            $features[] = $portal["title"];
                        }
                    }
                }

                $data['amenities'] = implode(',', $features);
                $data['size'] = $value['built_up_area'] ?? '';
                $data['bedroom'] = collect($value["bed"]["portals"] ?? [])->where(
                    "portal_id",
                    $mainPortal->id
                )->first()["name"] ?? 0;
                $data['bathroom'] = collect($value["bath"]["portals"] ?? [])->where(
                    "portal_id",
                    $mainPortal->id
                )->first()["name"] ?? 0;

                if ($value['agent']) {
                    $data['agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                    $data['agent']['email'] = $value['agent']['secondary_email'] != "" ? $value['agent']['secondary_email'] : $value['agent']['email'] ?? '';
                    $data['agent']['phone'] = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
                    $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                    $data['agent']['photo'] = $value['agent']['image_url'] ?? '';
                    if ($value['agent']['pp_enabled']) {
                        $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                        $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                        $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? '';
                    }
                }
                $data['developer'] = $value['details']['developer'] ?? null;
                $data['parking'] = $value['parking'] ?? 0;

                if ($value['furnished'] == 'FURNISHED') {
                    $data['furnished'] = 'Yes';
                } else {
                    if ($value['furnished'] == 'SEMI_FURNISHED') {
                        $data['furnished'] = 'Partly';
                    } else {
                        $data['furnished'] = 'No';
                    }
                }
                $data['photo'] = [];
                if ($value['photos']) {
                    $url = [];
                    foreach ($value['photos'] as $key => $photo) {
                        $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s', strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                        $dd['@value'] = $photo['path'];
                        $url['url'][] = $dd;
                    }
                    $data['photo'] = $url;
                }

                $prop['property'][] = $data;
                $data['geopoints'] = '';
                if (!empty($value["community_property"])) {
                    if ($value["community_property"]["latitude"] && $value["community_property"]["longitude"]) {
                        $data['geopoints'] = $value["community_property"]["latitude"] . ", " . $value["community_property"]["longitude"];
                    }
                } elseif (!empty($value["sub_community"])) {
                    if ($value["sub_community"]["latitude"] && $value["sub_community"]["longitude"]) {
                        $data['geopoints'] = $value["sub_community"]["latitude"] . ", " . $value["sub_community"]["longitude"];
                    }
                } else {
                    $data['geopoints'] = '';
                }
            }

            $last_update = $last_update['updated_at'];
            $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update));
            $pfObj['listing_count'] = $listing_count;

            $xml = $this->createXML($pfObj, 'list', $prop);
            $xmlContent = $xml->saveXML();

            // Save XML content to file
            $filePath = storage_path('app/public/xml/private_site.xml');
            file_put_contents($filePath, $xmlContent);

            return response()->json(['message' => 'XML file generated successfully', 'file_path' => $filePath], 200);
        } catch (\Exception $e) {
            info($e);
            return response()->json(['error' => 'Failed to generate XML file', 'msg' => $e->getMessage()], 500);
        }
    }

    public function getPrivateSiteXMLDataFromFile(Request $request)
    {
        $mainPortal = Portal::where("slug", "private-site")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        try {
            $filePath = storage_path('app/public/xml/private_site.xml');
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'XML file not found'], 404);
            }

            $xmlContent = file_get_contents($filePath);
            return response($xmlContent, 200)->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve XML data'], 500);
        }
    }

    public function generateGenericPortalXMLFile()
    {
        try {
            $mainPortal = Portal::where("slug", "generic-portal")->latest()->first();
            if (is_null($mainPortal)) {
                abort(404);
            }

            $last_update = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
                ->orderBy('updated_at', 'desc')
                ->select('updated_at')
                ->first()
                ->toArray();

            $listing_count = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
                ->where(['is_live' => true])
                ->count();

            $properties = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
                ->where(['is_live' => true])
                ->with([
                    'agent' => function ($q) {
                        $q->select('id', 'name', 'first_name', 'last_name', 'pp_enabled', 'pp_name', 'pp_photo', 'pp_license_no', 'email', 'secondary_email', 'license_no', 'mobile', 'bio', 'thumbnail', 'image');
                    },
                    'details',
                    'propertyCategory.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'title', 'property_category_id', 'portal_id');
                    },
                    'bed.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                    },
                    'developer',
                    'price' => function ($q) {
                        $q->select('id', 'property_id', 'price', 'prices', 'price_on_application');
                    },
                    'bath.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                    },
                    'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function ($q) use ($mainPortal) {
                            $q->where('portal_id', '=', $mainPortal->id);
                        });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)->where('title', '!=', '');
                    },
                    'community.region',
                    'subCommunity',
                    'communityProperty',
                    'photos' => function ($q) {
                        $q->select('id', 'property_id', 'path', 'title', 'updated_at');
                    },
                    'floorPlan',
                ])
                ->orderBy('properties.created_at', 'desc')
                ->get();

            $prop = [];
            foreach ($properties as $value) {
                $data = [];
                $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($value['updated_at']))];
                $data['permit_number'] = $value['rera_permit_number'] ?? '';
                $data['reference_number'] = $value['ref_no'];
                $data['price'] = [];
                if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                    $data['price'] = $value['price']["price"] ?? 0;
                    $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
                } else {
                    $data['price']['yearly'] = $value['price']["price"] ?? 0;
                    $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
                }
                $data['property_type'] = $value['propertyCategory']['portals'][0]["title"] ?? "";
                $data['price_on_application'] = 'No';

                $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
                $data['community']['@cdata'] = $value['community']['name'] ?? '';
                $data['sub_community']['@cdata'] = $value['subCommunity']['name'] ?? '';
                $data['property_name']['@cdata'] = $value['communityProperty']['name'] ?? '';

                $data['title_en']['@cdata'] = $value['title'] ?? '';
                $description = strip_tags($value['description'], '<br>');
                $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $description) ?? '';

                $data['completion_status'] = $value['completion_status'] ?? "";

                $features = [];
                foreach ($value["amenities"] ?? [] as $amenity) {
                    $features[] = $amenity["amenity"]["portals"][0]["title"];
                }
                $data['amenities'] = implode(',', $features);
                $data['size'] = $value['built_up_area'] ?? '';
                $data['bedroom'] = $value["bed"]["portals"][0]["name"] ?? 0;
                $data['bathroom'] = $value["bath"]["portals"][0]["name"] ?? 0;

                if ($value['agent']) {
                    if ($value['agent']['pp_enabled']) {
                        $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                        $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                        $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? '';
                    } else {
                        $data['agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                        $data['agent']['photo'] = $value['agent']['image_url'] ?? '';
                        $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                    }
                    $data['agent']['email'] = $value['agent']['secondary_email'] != "" ? $value['agent']['secondary_email'] : $value['agent']['email'] ?? '';
                    $data['agent']['phone'] = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
                }

                $data['developer'] = $value['details']['developer'] ?? null;
                $data['parking'] = $value['parking'] ?? 0;

                if ($value['furnished'] == 'FURNISHED') {
                    $data['furnished'] = 'Yes';
                } else if ($value['furnished'] == 'SEMI_FURNISHED') {
                    $data['furnished'] = 'Partly';
                } else {
                    $data['furnished'] = 'No';
                }

                $data['photo'] = [];
                if ($value['photos']) {
                    $url = [];
                    foreach ($value['photos'] as $photo) {
                        $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s', strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                        $dd['@value'] = $photo['path'];
                        $url['url'][] = $dd;
                    }
                    $data['photo'] = $url;
                }

                if (!empty($value["communityProperty"])) {
                    $data['geopoints'] = $value["communityProperty"]["latitude"] . ", " . $value["communityProperty"]["longitude"];
                } elseif (!empty($value["subCommunity"])) {
                    $data['geopoints'] = $value["subCommunity"]["latitude"] . ", " . $value["subCommunity"]["longitude"];
                } else {
                    $data['geopoints'] = '';
                }

                $prop['property'][] = $data;
            }

            $pfObj = [
                'last_update' => date('Y-m-d H:i:s', strtotime($last_update['updated_at'])),
                'listing_count' => $listing_count,
            ];

            $xml = $this->createXML($pfObj, 'list', $prop);
            $xmlContent = $xml->saveXML();

            // Save XML content to file
            $filePath = storage_path('app/public/xml/generic_portal.xml');
            file_put_contents($filePath, $xmlContent);

            return response()->json(['message' => 'XML file generated successfully', 'file_path' => $filePath], 200);
        } catch (\Exception $e) {
            info($e);
            return response()->json(['error' => 'Failed to generate XML file', 'msg' => $e->getMessage()], 500);
        }
    }

    public function getGenericPortalXMLDataFromFile(Request $request)
    {
        $mainPortal = Portal::where("slug", "generic-portal")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        try {
            $filePath = storage_path('app/public/xml/generic_portal.xml');
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'XML file not found'], 404);
            }

            $xmlContent = file_get_contents($filePath);
            return response($xmlContent, 200)->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve XML data'], 500);
        }
    }

    public function generateRightMoveXMLFile()
    {
        try {
            $mainPortal = Portal::where("slug", "right-move")->latest()->first();
            if (is_null($mainPortal)) {
                abort(404);
            }

            $last_update = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })->orderBy('updated_at', 'desc')
                ->select('updated_at')->first()->toArray();

            $listing_count = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })->where(['is_live' => true])->count();

            $properties = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
                ->where(['is_live' => true])
                ->with([
                    'agent' => function ($q) {
                        $q->select('id', 'name', 'first_name', 'last_name', 'pp_enabled', 'pp_name', 'pp_photo', 'pp_license_no', 'email', 'secondary_email', 'license_no', 'mobile', 'bio');
                    },
                    'details',
                    'propertyCategory.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'title', 'property_category_id', 'portal_id');
                    },
                    'bed.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                    },
                    'developer',
                    'price' => function ($q) {
                        $q->select('id', 'property_id', 'price', 'prices', 'price_on_application');
                    },
                    'bath.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                    },
                    'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function ($q) use ($mainPortal) {
                            $q->where('portal_id', '=', $mainPortal->id);
                        });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                            ->where('title', '!=', '');
                    },
                    'community.region', 'subCommunity', 'communityProperty',
                    'photos' => function ($q) {
                        $q->select('id', 'property_id', 'path', 'title', 'updated_at');
                    },
                    'floorPlan',
                ])
                ->orderBy('properties.created_at', 'desc')
                ->get();

            $prop = [];
            foreach ($properties as $value) {
                $data = [];
                $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($value['updated_at']))];
                $data['permit_number'] = $value['rera_permit_number'] ?? '';
                $data['reference_number'] = $value['ref_no'];
                $data['price'] = [];
                if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                    $data['price'] = $value['price']["price"] ?? 0;
                    $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
                } else {
                    $data['price']['yearly'] = $value['price']["price"] ?? 0;
                    $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
                }
                $data['property_type'] = $value['propertyCategory']['portals'][0]["title"] ?? "";
                $data['price_on_application'] = 'No';

                $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
                $data['community']['@cdata'] = $value['community']['name'] ?? '';
                $data['sub_community']['@cdata'] = $value['subCommunity']['name'] ?? '';
                $data['property_name']['@cdata'] = $value['communityProperty']['name'] ?? '';

                $data['title_en']['@cdata'] = $value['title'] ?? '';
                $description = strip_tags($value['description'], '<br>');
                $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $description) ?? '';

                $data['completion_status'] = $value['completion_status'] ?? "";

                $features = [];
                foreach ($value["amenities"] ?? [] as $amenity) {
                    $features[] = $amenity["amenity"]["portals"][0]["title"];
                }
                $data['amenities'] = implode(',', $features);
                $data['size'] = $value['built_up_area'] ?? '';
                $data['bedroom'] = $value["bed"]["portals"][0]["name"] ?? 0;
                $data['bathroom'] = $value["bath"]["portals"][0]["name"] ?? 0;

                if ($value['agent']) {
                    if ($value['agent']['pp_enabled']) {
                        $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                        $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                        $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? '';
                    } else {
                        $data['agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                        $data['agent']['photo'] = $value['agent']['image_url'] ?? '';
                        $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                    }
                    $data['agent']['email'] = $value['agent']['secondary_email'] != "" ? $value['agent']['secondary_email'] : $value['agent']['email'] ?? '';
                    $data['agent']['phone'] = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
                }

                $data['developer'] = $value['details']['developer'] ?? null;
                $data['parking'] = $value['parking'] ?? 0;

                if ($value['furnished'] == 'FURNISHED') {
                    $data['furnished'] = 'Yes';
                } else if ($value['furnished'] == 'SEMI_FURNISHED') {
                    $data['furnished'] = 'Partly';
                } else {
                    $data['furnished'] = 'No';
                }

                $data['photo'] = [];
                if ($value['photos']) {
                    $url = [];
                    foreach ($value['photos'] as $photo) {
                        $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s', strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                        $dd['@value'] = $photo['path'];
                        $url['url'][] = $dd;
                    }
                    $data['photo'] = $url;
                }

                if (!empty($value["communityProperty"])) {
                    $data['geopoints'] = $value["communityProperty"]["latitude"] . ", " . $value["communityProperty"]["longitude"];
                } elseif (!empty($value["subCommunity"])) {
                    $data['geopoints'] = $value["subCommunity"]["latitude"] . ", " . $value["subCommunity"]["longitude"];
                } else {
                    $data['geopoints'] = '';
                }

                $prop['property'][] = $data;
            }

            $pfObj = [
                'last_update' => date('Y-m-d H:i:s', strtotime($last_update['updated_at'])),
                'listing_count' => $listing_count,
            ];

            $xml = $this->createXML($pfObj, 'list', $prop);
            $xmlContent = $xml->saveXML();

            // Save XML content to file
            $filePath = storage_path('app/public/xml/right_move.xml');
            file_put_contents($filePath, $xmlContent);

            return response()->json(['message' => 'XML file generated successfully', 'file_path' => $filePath], 200);
        } catch (\Exception $e) {
            info($e);
            return response()->json(['error' => 'Failed to generate XML file', 'msg' => $e->getMessage()], 500);
        }
    }

    public function getRightMoveXMLDataFromFile(Request $request)
    {
        $mainPortal = Portal::where("slug", "right-move")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        try {
            $filePath = storage_path('app/public/xml/right_move.xml');
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'XML file not found'], 404);
            }

            $xmlContent = file_get_contents($filePath);
            return response($xmlContent, 200)->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve XML data'], 500);
        }
    }

    public function generateJamesEditionXMLFile()
    {
        try {
            $mainPortal = Portal::where("slug", "james-edition")->latest()->first();
            if (is_null($mainPortal)) {
                abort(404);
            }

            $last_update = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })->orderBy('updated_at', 'desc')
                ->select('updated_at')->first()->toArray();

            $listing_count = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })->where(['is_live' => true])->count();

            $prop = [];
            $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
                ->where(['is_live' => true])
                ->with([
                    'agent' => function ($q) {
                        $q->select('id', 'name', 'first_name', 'last_name', 'pp_enabled', 'pp_name', 'pp_photo', 'pp_license_no', 'email', 'secondary_email', 'license_no', 'mobile', 'bio', 'thumbnail', 'image');
                    },
                    'details',
                    'propertyCategory' => function ($q) {
                        $q->select('id');
                    },
                    'propertyCategory.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'title', 'property_category_id', 'portal_id');
                    },
                    'bed' => function ($q) {
                        $q->select('id');
                    },
                    'bed.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                    }, 'developer',
                    'price' => function ($q) {
                        $q->select('id', 'property_id', 'price', 'prices', 'price_on_application');
                    },
                    'bath' => function ($q) {
                        $q->select('id');
                    },
                    'bath.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                    },
                    'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function ($q) use ($mainPortal) {
                            $q->where('portal_id', '=', $mainPortal->id);
                        });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                            ->where('title', '!=', '');
                    },
                    'community.region', 'subCommunity', 'communityProperty',
                    'photos' => function ($q) {
                        $q->select('id', 'property_id', 'path', 'title', 'updated_at');
                    },
                    'floorPlan'])
                ->orderBy('properties.created_at', 'desc')
                ->get();

            foreach ($pp as $key => $value) {
                $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($value['updated_at']))];
                $data['permit_number'] = $value['rera_permit_number'] ?? '';
                $data['reference_number'] = $value['ref_no'];
                $data['price'] = [];
                if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                    $data['price'] = $value['price']["price"] ?? 0;
                    $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
                } else {
                    $data['price']['yearly'] = $value['price']["price"] ?? 0;
                    $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
                }
                $data['property_type'] = $value['propertyCategory']['portals'][0]["title"] ?? "";
                $data['price_on_application'] = 'No';

                $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
                $data['community']['@cdata'] = $value['community']['name'] ?? '';
                $data['sub_community']['@cdata'] = $value['subCommunity']['name'] ?? '';
                $data['property_name']['@cdata'] = $value['communityProperty']['name'] ?? '';

                $data['title_en']['@cdata'] = $value['title'] ?? '';

                $description = strip_tags($value['description'], '<br>');
                $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $description) ?? '';

                $data['completion_status'] = $value['completion_status'] ?? "";
                if ($value['video_link']) {
                    $data['youtube_video'] = $value['video_link'] ?? null;
                }
                $features = [];
                foreach ($value["amenities"] ?? [] as $amenity) {
                    $amenityType = $amenity["amenity"]["amenity_type"] ?? [];
                    $features[] = $amenity["amenity"]["portals"][0]["title"];
                }
                $data['amenities'] = implode(',', $features);
                $data['size'] = $value['built_up_area'] ?? '';
                $data['bedroom'] = $value["bed"]["portals"][0]['name'] ?? 0;
                $data['bathroom'] = $value["bath"]["portals"][0]["name"] ?? 0;

                if ($value['agent']) {
                    if ($value['agent']['pp_enabled']) {
                        $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                        $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                        $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? '';
                    } else {
                        $data['agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                        $data['agent']['photo'] = $value['agent']['image_url'] ?? '';
                        $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                    }
                    $data['agent']['email'] = $value['agent']['secondary_email'] != "" ? $value['agent']['secondary_email'] : $value['agent']['email'] ?? '';
                    $data['agent']['phone'] = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
                }
                $data['developer'] = $value['details']['developer'] ?? null;
                $data['parking'] = $value['parking'] ?? 0;

                if ($value['furnished'] == 'FURNISHED') {
                    $data['furnished'] = 'Yes';
                } else {
                    if ($value['furnished'] == 'SEMI_FURNISHED') {
                        $data['furnished'] = 'Partly';
                    } else {
                        $data['furnished'] = 'No';
                    }
                }
                $data['photo'] = [];
                if ($value['photos']) {
                    $url = [];
                    foreach ($value['photos'] as $key => $photo) {
                        $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s', strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                        $dd['@value'] = $photo['path'];
                        $url['url'][] = $dd;
                    }
                    $data['photo'] = $url;
                }

                $prop['property'][] = $data;
                if (!empty($value["communityProperty"])) {
                    $data['geopoints'] = $value["communityProperty"]["latitude"] . ", " . $value["communityProperty"]["longitude"];
                } elseif (!empty($value["subCommunity"])) {
                    $data['geopoints'] = $value["subCommunity"]["latitude"] . ", " . $value["subCommunity"]["longitude"];
                } else {
                    $data['geopoints'] = '';
                }
            }

            $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update['updated_at']));
            $pfObj['listing_count'] = $listing_count;

            $xml = $this->createXML($pfObj, 'list', $prop);
            $xmlContent = $xml->saveXML();

            // Save XML content to file
            $filePath = storage_path('app/public/xml/james_edition.xml');
            file_put_contents($filePath, $xmlContent);

            return response()->json(['message' => 'XML file generated successfully', 'file_path' => $filePath], 200);
        } catch (\Exception $e) {
            info($e);
            return response()->json(['error' => 'Failed to generate XML file', 'msg' => $e->getMessage()], 500);
        }
    }
    public function getJamesEditionXMLDataFromFile(Request $request)
    {
        try {
            $filePath = storage_path('app/public/xml/james_edition.xml');

            if (!file_exists($filePath)) {
                abort(404, 'XML file not found');
            }

            return response()->stream(function () use ($filePath) {
                $handle = fopen($filePath, 'r');
                while (!feof($handle)) {
                    echo fread($handle, 1024);
                    ob_flush();
                    flush();
                }
                fclose($handle);
            }, 200, ['Content-Type' => 'text/xml']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve XML file', 'message' => $e->getMessage()], 500);
        }
    }

    public function generateHouzaXMLFile()
    {
        try {
            $mainPortal = Portal::where("slug", "houza")->latest()->first();
            if (is_null($mainPortal)) {
                abort(404);
            }

            $last_update = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })->orderBy('updated_at', 'desc')
                ->select('updated_at')->first();

            $listing_count = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })->where(['is_live' => true])->count();

            $prop = [];
            $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
                ->where(['is_live' => true])
                ->with([
                    'agent' => function ($q) {
                        $q->select('id', 'name', 'first_name', 'last_name', 'pp_enabled', 'pp_name', 'pp_photo', 'pp_license_no', 'email', 'secondary_email', 'license_no', 'mobile', 'bio', 'thumbnail', 'image');
                    },
                    'details',
                    'propertyCategory' => function ($q) {
                        $q->select('id');
                    },
                    'propertyCategory.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'title', 'property_category_id', 'portal_id');
                    },
                    'bed' => function ($q) {
                        $q->select('id');
                    },
                    'bed.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                    },
                    'developer',
                    'price' => function ($q) {
                        $q->select('id', 'property_id', 'price', 'prices', 'price_on_application');
                    },
                    'bath' => function ($q) {
                        $q->select('id');
                    },
                    'bath.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                    },
                    'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function ($q) use ($mainPortal) {
                            $q->where('portal_id', '=', $mainPortal->id);
                        });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                            ->where('title', '!=', '');
                    },
                    'community.region', 'subCommunity', 'communityProperty',
                    'photos' => function ($q) {
                        $q->select('id', 'property_id', 'path', 'title', 'updated_at');
                    },
                    'floorPlan'])
                ->orderBy('properties.created_at', 'desc')
                ->get();

            foreach ($pp as $key => $value) {
                $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($value['updated_at']))];
                $data['permit_number'] = $value['rera_permit_number'] ?? '';
                $data['reference_number'] = $value['ref_no'];
                $data['price'] = [];
                if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                    $data['price'] = $value['price']["price"] ?? 0;
                    $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
                } else {
                    $data['price']['yearly'] = $value['price']["price"] ?? 0;
                    $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
                }
                $data['property_type'] = $value['propertyCategory']['portals'][0]["title"] ?? "";
                $data['price_on_application'] = 'No';

                $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
                $data['community']['@cdata'] = $value['community']['name'] ?? '';
                $data['sub_community']['@cdata'] = $value['subCommunity']['name'] ?? '';
                $data['property_name']['@cdata'] = $value['communityProperty']['name'] ?? '';

                $data['title_en']['@cdata'] = $value['title'] ?? '';

                $description = strip_tags($value['description'], '<br>');
                $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $description) ?? '';

                $data['completion_status'] = $value['completion_status'] ?? "";
                $features = [];
                foreach ($value["amenities"] ?? [] as $amenity) {
                    $amenityType = $amenity["amenity"]["amenity_type"] ?? [];
                    $features[] = $amenity["amenity"]["portals"][0]["title"];
                }
                $data['amenities'] = implode(',', $features);
                $data['size'] = $value['built_up_area'] ?? '';
                $data['bedroom'] = $value["bed"]["portals"][0]["name"] ?? 0;
                $data['bathroom'] = $value["bath"]["portals"][0]["name"] ?? 0;

                if ($value['agent']) {
                    if ($value['agent']['pp_enabled']) {
                        $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                        $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                        $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? '';
                    } else {
                        $data['agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                        $data['agent']['photo'] = $value['agent']['image_url'] ?? '';
                        $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                    }
                    $data['agent']['email'] = $value['agent']['secondary_email'] != "" ? $value['agent']['secondary_email'] : $value['agent']['email'] ?? '';
                    $data['agent']['phone'] = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
                }
                $data['developer'] = $value['details']['developer'] ?? null;
                $data['parking'] = $value['parking'] ?? 0;

                if ($value['furnished'] == 'FURNISHED') {
                    $data['furnished'] = 'Yes';
                } else {
                    if ($value['furnished'] == 'SEMI_FURNISHED') {
                        $data['furnished'] = 'Partly';
                    } else {
                        $data['furnished'] = 'No';
                    }
                }
                $data['photo'] = [];
                if ($value['photos']) {
                    $url = [];
                    foreach ($value['photos'] as $key => $photo) {
                        $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s', strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                        $dd['@value'] = $photo['path'];
                        $url['url'][] = $dd;
                    }
                    $data['photo'] = $url;
                }

                $prop['property'][] = $data;
                if (!empty($value["communityProperty"])) {
                    $data['geopoints'] = $value["communityProperty"]["latitude"] . ", " . $value["communityProperty"]["longitude"];
                } elseif (!empty($value["subCommunity"])) {
                    $data['geopoints'] = $value["subCommunity"]["latitude"] . ", " . $value["subCommunity"]["longitude"];
                } else {
                    $data['geopoints'] = '';
                }
            }

            $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update['updated_at']));
            $pfObj['listing_count'] = $listing_count;

            $xml = $this->createXML($pfObj, 'list', $prop);
            $xmlContent = $xml->saveXML();

            // Save XML content to file
            $filePath = storage_path('app/public/xml/houza.xml');
            file_put_contents($filePath, $xmlContent);

            return response()->json(['message' => 'XML file generated successfully', 'file_path' => $filePath], 200);
        } catch (\Exception $e) {
            info($e);
            return response()->json(['error' => 'Failed to generate XML file', 'msg' => $e->getMessage()], 500);
        }
    }
    public function getHouzaXMLDataFromFile(Request $request)
    {
        $mainPortal = Portal::where("slug", "houza")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        try {
            $filePath = storage_path('app/public/xml/houza.xml');
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'XML file not found'], 404);
            }

            $xmlContent = file_get_contents($filePath);
            return response($xmlContent, 200)->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve XML data'], 500);
        }
    }

    public function generateEmiratesEstateXMLFile()
    {
        try {
            $mainPortal = Portal::where("slug", "emiratesestate")->latest()->first();
            $genericPortal = Portal::where("slug", "generic-portal")->latest()->first();
            if (is_null($mainPortal)) {
                abort(404);
            }
            //  abort_if($mainPortal->xml_url != $request->key, 404);

            $last_update = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })->orderBy('updated_at', 'desc')
                ->select('updated_at')->first()->toArray();

            $listing_count = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })->where(['is_live' => true])->count();

            $prop = [];

            $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
                ->where(['is_live' => true])
                ->with([
                    'agent' => function ($q) {
                        $q->select('id', 'name', 'first_name', 'last_name', 'pp_enabled', 'pp_name', 'pp_photo', 'pp_license_no', 'email', 'secondary_email', 'license_no', 'mobile', 'bio', 'thumbnail', 'image');
                    },
                    'details',
                    'propertyCategory' => function ($q) {
                        $q->select('id');
                    },
                    'propertyCategory.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'title', 'property_category_id', 'portal_id');
                    },
                    'bed' => function ($q) {
                        $q->select('id');
                    },
                    'bed.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                    }, 'developer',
                    'price' => function ($q) {
                        $q->select('id', 'property_id', 'price', 'prices', 'price_on_application');
                    },
                    'bath' => function ($q) {
                        $q->select('id');
                    },
                    'bath.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                    },
                    'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function ($q) use ($mainPortal) {
                            $q->where('portal_id', '=', $mainPortal->id);
                        });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                            ->where('title', '!=', '');
                    },
                    'community.region', 'subCommunity', 'communityProperty',
                    'photos' => function ($q) {
                        $q->select('id', 'property_id', 'path', 'title', 'updated_at');
                    },
                    'floorPlan'])
                ->orderBy('properties.created_at', 'desc')
                ->get();
            foreach ($pp as $key => $value) {
                $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($value['updated_at']))];
                $data['permit_number'] = $value['rera_permit_number'] ?? '';
                $data['reference_number'] = $value['ref_no'];
                $data['price'] = [];
                if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                    $data['price'] = $value['price']["price"] ?? 0;
                    $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
                } else {
                    $data['price'] = $value['price']["price"] ?? 0;
                    $data['rental_period'] = 'Y';
                    $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
                }
                $data['property_type'] = $value['propertyCategory']['portals'][0]["title"] ?? "";
                $data['price_on_application'] = 'No';

                $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
                $data['community']['@cdata'] = $value['community']['name'] ?? '';
                $data['sub_community']['@cdata'] = $value['subCommunity']['name'] ?? '';
                $data['property_name']['@cdata'] = $value['communityProperty']['name'] ?? '';

                $data['title_en']['@cdata'] = $value['title'] ?? '';
                //$data['description_en']['@cdata'] = $value['description'] ?? '';

                $description = strip_tags($value['description'], '<br>');
                $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $description) ?? '';

                $data['completion_status'] = $value['completion_status'] ?? "";

                $features = [];

                foreach ($value["amenities"] ?? [] as $amenity) {
                    $amenityType = $amenity["amenity"]["amenity_type"] ?? [];
                    $features[] = $amenity["amenity"]["portals"][0]["title"];
                }

                $data['amenities'] = implode(',', $features);
                $data['size'] = $value['built_up_area'] ?? '';
                $data['bedroom'] = $value["bed"]["portals"][0]["name"] ?? 0;
                $data['bathroom'] = $value["bath"]["portals"][0]["name"] ?? 0;

                //$data['view'] = $value['view'] ?? '';

                if ($value['agent']) {
                    if ($value['agent']['pp_enabled']) {
                        $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                        $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                        $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? '';
                    } else {
                        $data['agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                        $data['agent']['photo'] = $value['agent']['image_url'] ?? '';
                        $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                    }

                    $data['agent']['email'] = $value['agent']['secondary_email'] != "" ? $value['agent']['secondary_email'] : $value['agent']['email'] ?? '';
                    $data['agent']['phone'] = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
                }
                $data['developer'] = $value['details']['developer'] ?? null;
                $data['parking'] = $value['parking'] ?? 0;

                if ($value['furnished'] == 'FURNISHED') {
                    $data['furnished'] = 'Yes';
                } else {
                    if ($value['furnished'] == 'SEMI_FURNISHED') {
                        $data['furnished'] = 'Partly';
                    } else {
                        $data['furnished'] = 'No';
                    }
                }
                $data['photo'] = [];
                if ($value['photos']) {
                    $url = [];
                    foreach ($value['photos'] as $key => $photo) {
                        $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s', strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                        $dd['@value'] = $photo['path'];
                        $url['url'][] = $dd;
                    }
                    $data['photo'] = $url;
                }

                $prop['property'][] = $data;
                if (!empty($value["communityProperty"])) {
                    $data['geopoints'] = $value["communityProperty"]["longitude"] . ", " . $value["communityProperty"]["latitude"];
                } elseif (!empty($value["subCommunity"])) {
                    $data['geopoints'] = $value["subCommunity"]["longitude"] . ", " . $value["subCommunity"]["latitude"];
                } else {
                    $data['geopoints'] = ''; //$value['latitude'] . ',' . $value['longitude'];
                }
            }

            $last_update = $last_update['updated_at'];
            $this->listing_count = $listing_count;
            $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update));
            $pfObj['listing_count'] = $listing_count;

            $xml = $this->createXML($pfObj, 'list', $prop);
            $xmlContent = $xml->saveXML();
            $filePath = storage_path('app/public/xml/emiratesEstate.xml');
            file_put_contents($filePath, $xmlContent);

            return response()->json(['message' => 'XML file generated successfully', 'file_path' => $filePath], 200);
        } catch (\Exception $e) {
            info($e);
            return response()->json(['error' => 'Failed to generate XML file', 'msg' => $e->getMessage()], 500);

        }
    }
    public function getEmiratesEstateXMLDataFromFile(Request $request)
    {
        $mainPortal = Portal::where("slug", "emiratesestate")->latest()->first();
        $genericPortal = Portal::where("slug", "generic-portal")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        // abort_if($mainPortal->xml_url != $request->key, 404);

        try {
            $filePath = storage_path('app/public/xml/emiratesEstate.xml');
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'XML file not found'], 404);
            }

            $xmlContent = file_get_contents($filePath);
            return response($xmlContent, 200)->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve XML data'], 500);
        }
    }

    public function generateYandexXMLFile()
    {
        $mainPortal = Portal::where("name", "Yandex")->latest()->first();
        if (is_null($mainPortal)) {
            echo "Portal not found";
            abort(404);
        }
        // abort_if($mainPortal->xml_url != $request->key, 404);

        $last_update = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->orderBy('updated_at', 'desc')
            ->select('updated_at')->first()?->toArray();

        $listing_count = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->where(['is_live' => true])->count();

        abort_if($listing_count == 0, 404);
        $prop = [];

        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })
            ->where(['is_live' => true])
            ->with([
                'agent' => function ($q) {
                    $q->select('id', 'name', 'first_name', 'last_name', 'pp_enabled', 'pp_name', 'pp_photo', 'pp_license_no', 'email', 'secondary_email', 'license_no', 'mobile', 'bio', 'thumbnail', 'image');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id');
                },
                'propertyCategory.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title', 'property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id', 'name');
                },
                'bed.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                }, 'developer',
                'price' => function ($q) {
                    $q->select('id', 'property_id', 'price', 'prices', 'price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id', 'name');
                },
                'bath.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                },
                'amenities' => function ($q) use ($mainPortal) {
                    $q->whereHas('amenity.portals', function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id);
                    });
                },
                'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', '=', $mainPortal->id)
                        ->where('title', '!=', '');
                },
                'community.region', 'subCommunity', 'communityProperty',
                'photos' => function ($q) {
                    $q->select('id', 'property_id', 'path', 'title', 'updated_at');
                },
                'floorPlan'])
            ->orderBy('properties.created_at', 'desc')
            ->get();

        foreach ($pp as $key => $value) { //->format('Y-m-d\TH:i:sP')

            $data['@attributes'] = ['internal-id' => $value['ref_no']];
            $data['category'] = !empty($value['propertyCategory']['portals']) ? strtolower($value['propertyCategory']['portals'][0]["title"]) : "";
            $data['type'] = $value['propertyCategory']['portals'][0]["title"] ?? "";

            if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                $data['type'] = 'primary_sell';
            } else {
                $data['type'] = 'rent';
            }
            $data['creation-date'] = date('Y-m-d\TH:i:sP', strtotime($value['created_at']));

            $data['location']['address'] = [
                '@attributes' => ['locale' => 'en'],
                '@cdata' => $value["communityProperty"]["path_name"] ?? $value["subCommunity"]["path_name"] ?? $value["community"]["path_name"] ?? '',
            ];
            //  $data['location']['address']['@attributes'] = ['locale' => 'en'];
            //  $data['location']['address'][] = $value["community_property"]["path_name"] ??  $value["sub_community"]["path_name"] ?? $value["community"]["path_name"] ?? '';
            $data['location']['latitude'] = $value["communityProperty"]["latitude"] ?? $value["subCommunity"]["latitude"] ?? $value["community"]["latitude"] ?? '';
            $data['location']['longitude'] = $value["communityProperty"]["longitude"] ?? $value["subCommunity"]["longitude"] ?? $value["community"]["longitude"] ?? '';
            if ($value['agent']) {

                if ($value['agent']['pp_enabled']) {
                    $data['sales-agent']['name']['@cdata'] = $value['agent']['pp_name'];
                    $data['sales-agent']['image']['@cdata'] = $value['agent']['pp_photo'] ?? '';
                } else {
                    $data['sales-agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                    $data['sales-agent']['image']['@cdata'] = $value['agent']['image_url'] ?? '';
                }
                if (strpos($value['agent']['mobile'], "971") === 0) {
                    $value['agent']['mobile'] = "+" . $value['agent']['mobile'];
                }
                $data['sales-agent']['phone'] = $value['agent']['mobile'];

            }
            if ($value['price']) {
                $data['price']['value'] = $value['price']['price'] ?? '';
                $data['price']['currency'] = 'AED';
                if ($data['type'] == 'rent') {
                    $data['price']['period'] = 'year';
                }

            }
            $data['area']['value'] = $value['built_up_area'];
            $data['area']['unit'] = 'sqf';

            $rooms = $value["bed"]["portals"][0]["name"] == "Studio" ? 0 : $value["bed"]["portals"][0]["name"];
            if ($rooms != 0) {
                $data['rooms'] = $rooms;
                $data['bedrooms'] = $value["bed"]["portals"][0]["name"] ?? '';
            }

            //$data['image']['@attributes'] = ['tag' => 'plan'];
            //write in this format    <image tag="plan">http://www.developer.ru/images/plans/000001289.jpg</image>
            //        <image>http://www.developer.ru/images/plans/0000013.jpg</image>
            if ($value['floorPlan']) {

                $data['image']['@attributes'] = ['tag' => 'plan'];
                $data['image']['@cdata'] = $value['floorPlan']['path'] ?? null;
            }
            $data['image'] = null;
            if ($value['photos']) {
                foreach ($value['photos'] as $key => $photo) {
                    if ($photo['path'] != null || $photo['path'] != '') {
                        $data['image'][] = $photo['path'] ?? '';
                    }

                }
            }

            $data['renovation'] = 'clean';

            $data['description'] = [
                '@attributes' => ['locale' => 'en'],
                '@cdata' => $value['description'] ?? '',
            ];

            // keep number only from string
            if ($value["bath"]["portals"][0]["name"] != 0) {
                $data['bathrooms'] = $value["bath"]["portals"][0]["name"] ?? '';
            }

            if ($value['floor']) {
                $data['floor'] = $value['floor'] ?? '';
            }
            //$data['floors-total'] =  '';
            //$data['completion-year'] =  '';
            //$data['completion-quarter'] =  '';
            //$data['lift'] = $value['lift'] ?? '';
            //$data['ceiling-height']['value'] = "" ?? '';
            //$data['ceiling-height']['unit'] = 'meter';
            //$data['living-space']['value'] = "" ?? '';
            //$data['living-space']['unit'] = 'sqm';
            //$data['kitchen-space']['value'] = "" ?? '';
            //$data['kitchen-space']['unit'] = 'sqm';
            //$data['balcony'] = $value['balcony'] ?? '';
            if ($value['bed']['name'] == 'Studio') {
                $data['studio'] = '1';
            } else {
                $data['studio'] = 'false';
            }

            //$data['building-state'] = $value['building_state'] ?? '';
            //$data['complex-id'] = "" ?? '';
            //$data['building-id'] = "" ?? '';
            //$data['virtual-tour'] =  '';
            //$data['virtual-tour']['model-url'] = $value['virtual_tour'] ?? '';
            //$data['virtual-tour']['provider'] = '';
            //$data['virtual-tour']['preview-url'] = $value['virtual_tour'] ?? '';

            if ($value['video_link']) {
                $data['youtube-video-review'] = $value['video_link'] ?? '';
            }

            foreach ($value["amenities"] ?? [] as $key => $amenity) {
                $data['facility'][$key] = $amenity["amenity"]["portals"][0]["title"] ?? '';
            }

            if ($value['parking']) {
                $data['parking-places'] = [];
                $data['parking-places']['@attributes'] = ['free' => 'yes'];
                $data['parking-places'] = $value['parking'] ?? "";
            }

            $prop[] = $data;

        }

        $last_update = $last_update['updated_at'];
        $this->listing_count = $listing_count;
        $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update));
        $pfObj['listing_count'] = $listing_count;

        $propt['generation-date'] = date('Y-m-d\TH:i:sP', strtotime(now()));
        $propt['offer'] = $prop;
        $xml = $this->createXML([], 'realty-feed', $propt);
        $xmlContent = $xml->saveXML();
        $filePath = storage_path('app/public/xml/Yandex.xml');
        file_put_contents($filePath, $xmlContent);
        return response()->json(['message' => 'XML file generated successfully', 'file_path' => $filePath], 200);
    }
    public function getYandexXMLDataFromFile(Request $request)
    {
        $mainPortal = Portal::where("name", "Yandex")->latest()->first();
        if (is_null($mainPortal)) {
            echo "Portal not found";
            abort(404);
        }
        abort_if($mainPortal->xml_url != $request->key, 404);

        try {
            $filePath = storage_path('app/public/xml/Yandex.xml');
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'XML file not found'], 404);
            }

            $xmlContent = file_get_contents($filePath);
            return response($xmlContent, 200)->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve XML data'], 500);
        }
    }

    public function generateBayutDubizzleCommercialXMLFile()
    {

        $mainPortal = Portal::where("name", "Bayut")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        // abort_if("bayxBIL7TZ3UMbaiLq" != $request->key, 404);

        $prop = [];

        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id)->orWhere('portal_id', 3);
        })
            ->where('is_live', true)
            ->whereHas('agent', function ($q) {
                $q->where('is_commercial', true);
            })
            ->whereHas('community.region', function ($q) {
                $q->where('name', 'Dubai');
            })
            ->with([
                'agent' => function ($q) {
                    $q->select('id', 'name', 'first_name', 'last_name', 'pp_enabled', 'pp_name', 'pp_photo', 'pp_license_no', 'email', 'secondary_email', 'license_no', 'mobile', 'bio', 'thumbnail', 'image');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id');
                },
                'propertyCategory.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title', 'property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                }, 'developer',
                'price' => function ($q) {
                    $q->select('id', 'property_id', 'price', 'prices', 'price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                },
                'bath.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                },
                'amenities' => function ($q) use ($mainPortal) {
                    $q->whereHas('amenity.portals', function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id);
                    });
                },
                'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', '=', $mainPortal->id)
                        ->where('title', '!=', '');
                },
                'community.region', 'subCommunity', 'communityProperty', 'portals',
                'photos' => function ($q) {
                    $q->select('id', 'property_id', 'path', 'title', 'updated_at');
                },
                'floorPlan'])
            ->orderBy('properties.created_at', 'desc')
            ->get();

        foreach ($pp as $key => $value) {
            $data['Property_Ref_No'] = $value?->ref_no;

            $data['Rent_Frequency']['@cdata'] = '';
            if ($value?->for == "Residential for sale" || $value?->for == "Commercial for sale") {
                $data['Property_purpose']['@cdata'] = 'Buy';
            } else {
                $data['Property_purpose']['@cdata'] = 'Rent';
                $data['Rent_Frequency']['@cdata'] = 'yearly';
            }
            $data['Property_Type']['@cdata'] = $value?->propertyCategory?->portals?->first()?->title ?? '';
            //$data['Property_Type']['@cdata'] = $value['property_category']['portals'][0]['title'] ?? '';
            $data['Property_Status']['@cdata'] = 'live';

            $data['City']['@cdata'] = $value?->community?->region?->name ?? 'Dubai';
            $data['Locality']['@cdata'] = $value?->community?->alternate_name ?? $value?->community?->name ?? '';
            $data['Sub_Locality']['@cdata'] = $value?->subCommunity?->alternate_name ?? $value?->subCommunity?->name ?? '';
            if (!$value?->communityProperty?->alternate_use) {
                $data['Tower_Name']['@cdata'] = '';
            } else {
                $data['Tower_Name']['@cdata'] = $value?->communityProperty?->alternate_name ?? $value?->communityProperty?->name ?? '';
            }
            $data['Property_Title']['@cdata'] = $value->title ?? '';
            //$data['Property_Description']['@cdata'] = $value['description'] ?? '';
            $description = strip_tags($value->description, '<br>');
            $updateDescription = str_replace("<br />", "\r\n", $description);
            $data['Property_Description']['@cdata'] = str_replace("<br>", "\r\n", $updateDescription) ?? '';
            if ($value->furnished == 'FURNISHED') {
                $data['Furnished']['@cdata'] = 'Yes';
            } else {
                if ($value['furnished'] == 'SEMI_FURNISHED') {
                    $data['Furnished']['@cdata'] = 'Partly';
                } else {
                    $data['Furnished']['@cdata'] = 'No';
                }
            }

            $data['Property_Size']['@cdata'] = str_replace(',', '', $value['built_up_area'] . '') ?? '';
            $data['Property_Size_Unit']['@cdata'] = "SQFT";

            $data['bedroom'] = $value?->bed?->portals?->first()?->name ?? 0;
            $data['bathroom'] = $value?->bath?->portals->first()->name ?? 0;

            $data['Price']['@cdata'] = ($value?->price?->price ?? '') . '';
            if ($value['agent']) {
                $data['Listing_Agent']['@cdata'] = $value?->agent?->first_name . ' ' . $value?->agent?->last_name;

                $data['Listing_Agent_Email']['@cdata'] = $value?->agent?->secondary_email != "" ? $value?->agent?->secondary_email : $value?->agent?->email;
                if ($value['agent']['pp_enabled']) {
                    $data['Listing_Agent']['@cdata'] = $value?->agent?->pp_name;
                }
                $data['Listing_Agent_Phone']['@cdata'] = $value?->agent?->mobile;
            }
            $data['Features']['Feature'] = array();
            foreach ($value["amenities"] ?? [] as $amenity) {

                $data['Features']['Feature'][]['@cdata'] = $amenity?->amenity?->portals?->first()?->title;
                // foreach ($amenity["amenity"]["portals"] ?? [] as $portal) {
                //     if ($portal["portal_id"] == $mainPortal->id && !empty($portal["title"])) {
                //         $data['Features']['Feature'][]['@cdata'] = $portal["title"];
                //     }
                // }
            }

            $data['Images'] = [];

            if ($value['photos']) {
                foreach ($value->photos ?? [] as $key => $photo) {
                    $data['Images']['Image'][]['@cdata'] = $photo?->path;
                }
            }

            $data['Videos']['Video']['@cdata'] = $value?->video_link;
            $data['Last_Updated']['@cdata'] = date('Y-m-d H:i:s', strtotime($value?->updated_at));
            $data['Permit_Number']['@cdata'] = $value['rera_permit_number'] ?? '';

            if ($value['completion_status'] == 'Off-Plan Primary' || $value?->completion_status == 'Off-Plan Secondary') {
                $data['Off_Plan']['@cdata'] = 'Yes';
            } else {
                $data['Off_Plan']['@cdata'] = 'No';
            }
            $data['Portals'] = [];

            foreach ($value['portals'] as $portal) {
                if ($portal['portal_id'] == 3) {
                    $data['Portals']['Portal'][]['@cdata'] = 'Dubizzle';
                }
                if ($portal['portal_id'] == 2) {
                    $data['Portals']['Portal'][]['@cdata'] = 'Bayut';
                }
            }

            $prop['Property'][] = $data;
        } // end foreach

        $xml = $this->createXML([], 'Properties', $prop);
        $xmlContent = $xml->saveXML();

        $filePath = storage_path('app/public/xml/BayutDubizzleCommercial.xml');
        file_put_contents($filePath, $xmlContent);

        return response()->json(['message' => 'XML file generated successfully', 'file_path' => $filePath], 200);
    }
    public function getBayutDubizzleCommercialXMLDataFromFile(Request $request)
    {
        info('BayutDubizzle Commercial is called at ' . now());

        $mainPortal = Portal::where("name", "Bayut")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        abort_if("bayxBIL7TZ3UMbaiLq" != $request->key, 404);

        try {
            $filePath = storage_path('app/public/xml/BayutDubizzleCommercial.xml');
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'XML file not found'], 404);
            }

            $xmlContent = file_get_contents($filePath);
            return response($xmlContent, 200)->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve XML data'], 500);
        }
    }

    public function generatePropertyFinderCommercialXMLFile()
    {
        try {
            $mainPortal = Portal::where("slug", "property-finder")->latest()->first();
            if (is_null($mainPortal)) {
                abort(404);
            }
            // abort_if('pFyxBIL7TZ3UMbaiLq' != $request->key, 404);
            $prop = [];

            $completionStatusMapping = [
                'Ready Primary' => 'completed_primary',
                'Ready Secondary' => 'completed',
                'Off-Plan Primary' => 'off_plan_primary',
                'Off-Plan Secondary' => 'off_plan',
            ];

            $furnishedMapping = [
                'FURNISHED' => 'Yes',
                'SEMI_FURNISHED' => 'Partly',
            ];

            $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
                ->where('is_live', true)
                ->where('completion_status', '!=', 'Off-Plan Primary')
                ->where('region_id', 1)
                ->whereHas('agent', function ($q) {
                    $q->where('is_commercial', true);
                })
                ->with([
                    'agent' => function ($q) {
                        $q->select('id', 'name', 'first_name', 'last_name', 'pp_enabled', 'pp_name', 'pp_photo', 'pp_license_no', 'email', 'secondary_email', 'license_no', 'mobile', 'bio', 'thumbnail', 'image');
                    },
                    'details',
                    'propertyCategory' => function ($q) {
                        $q->select('id');
                    },
                    'propertyCategory.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'title', 'property_category_id', 'portal_id');
                    },
                    'bed' => function ($q) {
                        $q->select('id');
                    },
                    'bed.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                    }, 'developer',
                    'price' => function ($q) {
                        $q->select('id', 'property_id', 'price', 'prices', 'price_on_application');
                    },
                    'bath' => function ($q) {
                        $q->select('id');
                    },
                    'bath.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                    },
                    'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function ($q) use ($mainPortal) {
                            $q->where('portal_id', '=', $mainPortal->id);
                        });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                            ->where('title', '!=', '');
                    },
                    'community.region', 'subCommunity', 'communityProperty',
                    'photos' => function ($q) {
                        $q->select('id', 'property_id', 'path', 'title', 'updated_at');
                    },
                    'floorPlan'])
                ->orderBy('properties.created_at', 'desc')
                ->get();
            $listing_count = $pp->count();
            $mostRecentListing = $pp->sortByDesc('updated_at')->first();

            foreach ($pp as $key => $value) {
                $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($mostRecentListing->updated_at))];
                $data['reference_number'] = $value['ref_no'];
                $data['permit_number'] = $value['rera_permit_number'] ?? '';
                $data['price'] = [];
                if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                    $data['price'] = $value['price']["price"] ?? 0;
                    $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
                } else {
                    $data['price']['yearly'] = $value['price']["price"] ?? 0;
                    $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
                    $data['cheques'] = $value['cheques'] ?? null;
                }
                $data['property_type'] = $value['propertyCategory']['portals'][0]["title"] ?? "";
                $data['price_on_application'] = $value['price']['price_on_application'] ? 'Yes' : 'No';

                $data['availability_date'] = $value?->availability_date;
                // $data['title_deed']['@cdata'] = $value?->title_deed_text ?? '';
                $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
                $data['community']['@cdata'] = $value['community']['name'] ?? '';
                $data['sub_community']['@cdata'] = $value['subCommunity']['name'] ?? '';

                if (($value?->communityProperty?->name ?? null) && (!$value?->communityProperty?->default_use)) {
                    $data['property_name']['@cdata'] = '';
                } else {
                    $data['property_name']['@cdata'] = $value['communityProperty']['name'] ?? '';
                }

                $data['title_en']['@cdata'] = $value['title'] ?? '';
                //$data['description_en']['@cdata'] = $value['description'] ?? '';

                $description = strip_tags($value['description'], '<br>');
                $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $description) ?? '';

                $data['completion_status'] = $completionStatusMapping[$value['completion_status']] ?? 'completed';
                $data['floor'] = $value['floor'] ?? '';
                $data['parking'] = $value['parking'] ?? 0;
                $data['furnished'] = $furnishedMapping[$value['furnished']] ?? 'No';
                $data['size'] = $value['built_up_area'] ?? '';
                $data['bedroom'] = $value["bed"]["portals"][0]["name"] ?? 0;
                $data['bathroom'] = $value["bath"]["portals"][0]["name"] ?? 0;
                $data['view'] = $value['view'] ?? '';
                $privateAmenities = [];
                $commercialAmenities = [];
                $features = [];

                foreach ($value["amenities"] ?? [] as $amenity) {
                    $amenityType = $amenity["amenity"]["amenity_type"] ?? [];
                    if ($amenityType == "private") {
                        $privateAmenities[] = $amenity["amenity"]["portals"][0]['title'];
                    }
                    if ($amenityType == "commercial") {
                        $commercialAmenities[] = $amenity["amenity"]["portals"][0]['title'];
                    }
                    $features[] = $amenity["amenity"]["portals"][0]["title"];
                }
                $data['private_amenities'] = implode(",", $privateAmenities);
                $data['commercial_amenities'] = implode(",", $commercialAmenities);
                $data['amenities'] = implode(',', $features);

                //$data['view'] = $value['view'] ?? '';

                if ($value['agent']) {
                    $data['agent']['id'] = $value['agent']['id'];

                    if ($value['agent']['pp_enabled']) {
                        $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                        $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                        $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? '';
                    } else {
                        $data['agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                        $data['agent']['photo'] = $value['agent']['image_url'] ?? '';
                        $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                    }
                    if ($value['agent']['secondary_email'] != "") {
                        $data['agent']['email'] = $value['agent']['secondary_email'] ?? '';
                    } else {
                        $data['agent']['email'] = $value['agent']['email'] ?? '';
                    }

                    $number = str_replace("+971", "971", $value['agent']['mobile']) ?? '';
                    $data['agent']['phone'] = str_replace(' ', '', $number);
                    $data['agent']['info'] = $value['agent']['bio'] ?? '';

                }
                $data['developer'] = $value['developer']['short_name'] ?? $value['details']['developer'] ?? null;
                $data['photo'] = [];
                if ($value['photos']) {
                    $url = [];
                    foreach ($value['photos'] as $key => $photo) {
                        $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s', strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                        $dd['@value'] = $photo['path'];
                        $url['url'][] = $dd;
                    }
                    $data['photo'] = $url;
                }

                $data['video_tour_url'] = $value['video_link'] ?? '';
                $data['view360'] = $value['video_360_link'] ?? '';

                $data['floor_plan'] = [];

                if ($value['floor_plan']) {
                    $url = [];
                    $dd['@attributes'] = ['last_updated' => $value['floor_plan']['updated_at'], 'watermark' => 'no'];
                    $dd['@value'] = $value['floor_plan']['path'] ?? null;
                    $url['url'][] = $dd;
                    $data['floor_plan'] = $url;
                }

                $prop['property'][] = $data;
                if (!empty($value["communityProperty"])) {
                    $data['geopoints'] = $value["communityProperty"]["latitude"] . ", " . $value["communityProperty"]["longitude"];
                } elseif (!empty($value["subCommunity"])) {
                    $data['geopoints'] = $value["subCommunity"]["latitude"] . ", " . $value["subCommunity"]["longitude"];
                } else {
                    $data['geopoints'] = ''; //$value['latitude'] . ',' . $value['longitude'];
                }
            }

            $last_update = $mostRecentListing->updated_at;
            $this->listing_count = $listing_count;
            $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update));
            $pfObj['listing_count'] = $listing_count;

            $xml = $this->createXML($pfObj, 'list', $prop);
            $xmlContent = $xml->saveXML();
            $filePath = storage_path('app/public/xml/propertyFinderCommercial.xml');
            file_put_contents($filePath, $xmlContent);

            return response()->json(['message' => 'XML file generated successfully', 'file_path' => $filePath], 200);
            return \Response::make($xxx, '200')->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            info($e);
            return response()->json(['error' => 'Failed to generate XML file', 'msg' => $e->getMessage()], 500);
        }
    }
    public function getPropertyFinderCommercialXMLDataFromFile(Request $request)
    {
        info('PropertyFinder Commercial is called at ' . now());

        $mainPortal = Portal::where("slug", "property-finder")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        abort_if('pFyxBIL7TZ3UMbaiLq' != $request->key, 404);

        try {
            $filePath = storage_path('app/public/xml/propertyFinderCommercial.xml');
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'XML file not found'], 404);
            }

            $xmlContent = file_get_contents($filePath);
            return response($xmlContent, 200)->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve XML data'], 500);
        }
    }
    private function sanitizeForXML($value, $maxLength = null)
    {
        if (empty($value)) {
            return null;
        }

        // Convert to string
        $value = (string) $value;

        // Remove all control characters except tab (0x09), newline (0x0A), and carriage return (0x0D)
        // This removes characters 0x00-0x08, 0x0B, 0x0C, 0x0E-0x1F, and 0x7F
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/', '', $value);

        // Also remove any remaining non-printable Unicode characters
        $value = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]/u', '', $value);

        // Remove or replace problematic characters that can break XML
        $value = str_replace([
            "\x02", "\x03", "\x04", "\x05", "\x06", "\x07", "\x08", // ASCII 2-8
            "\x0B", "\x0C", "\x0E", "\x0F", // ASCII 11,12,14,15
            "\x10", "\x11", "\x12", "\x13", "\x14", "\x15", "\x16", "\x17", // ASCII 16-23
            "\x18", "\x19", "\x1A", "\x1B", "\x1C", "\x1D", "\x1E", "\x1F"  // ASCII 24-31
        ], '', $value);

        // Clean up any remaining invalid XML characters
        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');

        // Escape XML entities
        $value = htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');

        // Trim to max length if specified
        if ($maxLength) {
            $value = mb_substr($value, 0, $maxLength, 'UTF-8');
        }

        return $value;
    }


    public function generateMoveXMLFile()
    {
        $mainPortal = Portal::where("name", "Move")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }

        $prop = [];

        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id)->orWhere('portal_id', 3);
        })
            ->where('is_live', true)
            ->whereHas('community.region', function ($q) {
                $q->where('name', 'Dubai');
            })
            ->with([
                'agent' => function ($q) {
                    $q->select('id', 'name', 'first_name', 'last_name', 'pp_enabled', 'pp_name', 'pp_photo', 'pp_license_no', 'email', 'secondary_email', 'license_no', 'mobile', 'bio', 'thumbnail', 'image');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id');
                },
                'propertyCategory.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title', 'property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                }, 'developer',
                'price' => function ($q) {
                    $q->select('id', 'property_id', 'price', 'prices', 'price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                },
                'bath.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                },
                'amenities' => function ($q) use ($mainPortal) {
                    $q->whereHas('amenity.portals', function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id);
                    });
                },
                'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', '=', $mainPortal->id)
                        ->where('title', '!=', '');
                },
                'community.region', 'subCommunity', 'communityProperty', 'portals',
                'photos' => function ($q) {
                    $q->select('id', 'property_id', 'path', 'title', 'updated_at');
                },
                'floorPlan'])
            ->orderBy('properties.created_at', 'desc')
            ->get();




        foreach ($pp as $key => $value) {
            $data = array();
            $data['ReferenceID'] = (int)$value->id; //must be numeric value
            $price = $value?->price?->price ?? 0;
            if ($value->for == 'Residential for rent' || $value->for == 'Commercial for rent') {
               $listing_for = 'rent';

                $price = $value?->price?->price ?? 0;
                $price = $price/12; // monthly price
            } else if ($value->for == 'Commercial for sale' || $value->for == 'Residential for sale') {
                $listing_for = 'sale';
            } else {
                $listing_for = 'sale';
            }
            if ($value['communityProperty']){
                $data['ListingAddress'] = $value['communityProperty']['path_name'];
                $data['Latitude'] = $value['communityProperty']['latitude'];
                $data['Longitude'] = $value['communityProperty']['longitude'];
            }elseif ($value['subCommunity']){
                $data['ListingAddress'] = $value['subCommunity']['path_name'];
                $data['Latitude'] = $value['subCommunity']['latitude'];
                $data['Longitude'] = $value['subCommunity']['longitude'];
            }elseif ($value['community']){
                $data['ListingAddress'] = $value['community']['path_name'];
                $data['Latitude'] = $value['community']['latitude'];
                $data['Longitude'] = $value['community']['longitude'];
            }else{
                $data['ListingAddress'] = '';
                $data['Latitude'] = null;
                $data['Longitude'] = null;
            }

            $data['ListingAddress'] = substr($data['ListingAddress'] ?? '', 0, 250);
            $data['CityName']   = substr($value?->community?->name ?? '', 0, 100);
            $data['RegionName'] = substr($value?->community?->region?->name ?? 'Dubai', 0, 50);
            $data['CountryName'] = "AE";
            $data['PostalCode'] = null;
            $data['ListingPrice'] = round($price,2);
            $data['CurrencyCode'] = 'AED';
            $data['BuiltArea'] = $value?->built_up_area ?? 0;
            $data['LotArea'] = $value->plot_size;// This is marked as required but not sure what it is
            $data['UnitofMeasurement'] = 'Square Foot';
            $data['ListingType'] = $value?->for == "Residential for sale" || $value?->for == "Commercial for sale" ? 'For Sale' : 'For Rent';
           // $description = substr($value?->description ?? '', 0, 500);
            $data['PropertyDescription'] = $this->sanitizeForXML($value?->description, 499);

            $data['DrivingDirections'] = null;
            $data['Zoning'] = null;
            $data['Bedrooms'] = (int)$value?->bed?->portals?->first()?->name ?? 0;
            $data['Baths'] = (int)$value?->bath?->portals->first()?->name ?? 0;
            $data['Rooms'] = 0;
            $data['Parking'] = $value->parking;
            $data['Stories'] = null;
            $data['YearBuilt'] = $value->build_year;
            $data['HeatingFeatures'] = 'None';
            $data['Cooling'] = 'Blank';// This is marked as required but not sure what it is
            $data['Pool'] = 'Blank';// This is marked as required but not sure what it is
            $data['Furnished'] = $value->furnished == 'FURNISHED' ? 'Yes' : 'No';
            $data['Garden'] = 'Blank';// This is marked as required but not sure what it is
            $data['Garage'] = 'Blank';// This is marked as required but not sure what it is
            $data['Terrace'] = 'Blank';// This is marked as required but not sure what it is
            $data['InteriorFeatures'] = null;
            $data['ExteriorFeatures'] = null;
            $data['LotFeatures'] = null;
            $data['CommunityFeatures'] = null;
            $data['PropertyType'] = $value?->propertyCategory?->portals?->first()?->title ?? '';
            $data['Fireplaces'] = null;
            $data['TotalUnits'] = null;
            $data['BuildingName'] = $value->communityProperty ? $value->communityProperty->name : null;
            $data['Roofing'] = null;

            foreach ($value->amenities ?? [] as $amenity) {
                $amenityTitle = $amenity->amenity->portals->first()->title ?? '';
                if (stripos($amenityTitle, 'Heating') !== false) {
                    $data['HeatingFeatures'] = $amenityTitle;
                }
                if (stripos($amenityTitle, 'Cooling') !== false) {
                    $data['Cooling'] ="Yes";
                }
                if (stripos($amenityTitle, 'Pool') !== false) {
                    $data['Pool'] ="Yes";
                }
                if (stripos($amenityTitle, 'Garden') !== false) {
                    $data['Garden'] ="Yes";
                }
                if (stripos($amenityTitle, 'Garage') !== false) {
                    $data['Garage'] ="Yes";
                }
            }


            if ($value['photos']) {
                foreach ($value->photos ?? [] as $index => $photo) {
                    $data['ImageURL'. ($index+1)] = $photo?->path;
                }
            }

            $data['ListingStatus'] = 'ACTIVE';

            $prop['Property'][] = $data;
        }

        $xml = $this->createXML([], 'Properties', $prop, [], 'UTF-16');
        $xmlContent = $xml->saveXML();

        // Save XML to a file
        $filePath = storage_path('app/public/xml/move.xml');
        file_put_contents($filePath, $xmlContent);

        return response()->json(['message' => 'XML file generated successfully', 'file_path' => $filePath]);
    }

    public function getMoveXMLDataFromFile(Request $request)
    {
        info('Move is called at ' . now());
        $mainPortal = Portal::where("name", "Move")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        abort_if($mainPortal->xml_url != $request->key, 404);

        $filePath = storage_path('app/public/xml/move.xml');

        if (!file_exists($filePath)) {
            abort(404, 'XML file not found');
        }

        $xmlContent = file_get_contents($filePath);

        return response($xmlContent, 200)->header('Content-Type', 'text/xml');
    }
}
