<?php

namespace App\Traits;

use App\Models\Filter;
use App\Models\Property;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait FiltersTrait
{
    private function isArray($key)
    {
        return gettype(\request($key)) == 'array' && count(\request($key)) > 0;
    }

    private function getOperator($key)
    {
        return config('query.operator.' . $key);
    }

    private function getStatus($key)
    {
        if (in_array($key, ["null", "not_null", 'today', 'yesterday', 'last_week', 'last_month', 'this_month', 'this_week'])) {
            return true;
        } else {
            return false;
        }
    }

    private function queryBuilder($query, $column, $operator, $key, $json = null)
    {

        $value = $key;
        switch ($operator) {
            case 'equal':
                return $query->where($column, $value);
            case 'notEqual':
                return $query->where($column, '!=', $value);
            case '>':
                return $query->where($column, '>', $value);
            case '>=':
                return $query->where($column, '>=', $value);
            case '<':
                return $query->where($column, '<', $value);
            case '<=':
                return $query->where($column, '<=', $value);
            case 'like':
                return $query->where($column, 'LIKE', '%' . $value . '%');
            case 'ilike':
                return $query->where($column, 'NOT LIKE', '%' . $value . '%');
            case 'in':
                $value = is_array($value) ? $value : [$value];
                // if its $column is "for" then we need to check if the value is an array or not if array its still in same col as json array and also one value is in same col as json array
                // if ($column == 'for') {
                //     return $query->whereJsonContains($column, $value);
                // }

                if ($column == 'for' && $json != null) {
                    return $query->where(function ($q) use ($column, $value) {
                        $q->whereJsonContains($column, $value);
                        foreach ($value as $v) {
                            $q->orWhereJsonContains($column, $v);
                        }
                    });
                } else {
                    return $query->whereIn($column, $value);
                }

                return $query->whereIn($column, $value);
            case 'not_in':
                $value = is_array($value) ? $value : [$value];
                return $query->whereNotIn($column, $value);
            case 'between':
                $value = is_array($value) ? $value : explode(',', $value);
                return $query->whereBetween($column, $value);
            case 'not_between':
                $value = is_array($value) ? $value : explode(',', $value);
                return $query->whereNotBetween($column, $value);
            case 'contains':
                $cleanedValue = preg_replace('/[^a-zA-Z0-9]/', '', $value);
                return $query->whereRaw("REPLACE(REPLACE($column, ' ', ''), '&', '') LIKE ?", ['%' . $cleanedValue . '%']);

                // $cleanedColumn = preg_replace('/[^a-zA-Z0-9]/', '', str_replace(' ', '', $column));
                // return $query->whereRaw("REPLACE(REPLACE($cleanedColumn, '&', '') LIKE ?", ['%' . $value . '%']);
            case 'in_range':
                $first_val = $value[0];
                $second_val = $value[1];
                return $query->whereBetween($column, [floatval($first_val), floatval($second_val)]);
            case 'not_in_range':
                $first_val = $value[0];
                $second_val = $value[1];
                return $query->whereNotBetween($column, [floatval($first_val), floatval($second_val)]);
            case 'null':
                // return $query->whereNull($column)->orWhere($column, '')->orWhere($column, '-');
                return $query->where(function ($q) use ($column) {
                    $q->whereNull($column)->orWhere($column, '')->orWhere($column, '-');
                });
            case 'not_null':
                return $query->whereNotNull($column);
            case 'due_in':
                return $query->whereDate($column, '>', now()->subDays($value));
            case 'on':
                $value = is_array($value) ? $value : explode(' to ', $value);
                if (count($value) === 1) {
                    return $query->whereDate($column, '=', $value[0]);
                } else {
                    return $query->whereDate($column, '>=', $value[0])->whereDate($column, '<=', $value[1]);
                }
                return $query->whereDate($column, '=', $value);
            case 'before':
                return $query->whereDate($column, '<', $value);
            case 'after':
                return $query->whereDate($column, '>', $value);

            case 'before_start':
                return $query->whereDate('contract_start', '<', $value);
            case 'after_start':
                return $query->whereDate('contract_start', '>', $value);
            case 'before_end':
                return $query->whereDate('contract_end', '<', $value);
            case 'after_end':
                return $query->whereDate('contract_end', '>', $value);

            case 'between_dates':
                $value = is_array($value) ? $value : explode(' to ', $value);
                // dd($value);
                if (count($value) === 1) {
                    // $startOfDay = $value[0] . ' 00:00:00';
                    // $endOfDay = $value[0] . ' 23:59:59';
                    // return $query->where($column, '>=', $startOfDay)->where($column, '<=', $endOfDay);
                    $startOfDay = date('Y-m-d', strtotime($value[0])) . ' 00:00:00';
                    $endOfDay = date('Y-m-d', strtotime($value[0])) . ' 23:59:59';
                    return $query->where($column, '>=', $startOfDay)->where($column, '<=', $endOfDay);
                } else {
                    // $startOfDay = $value[0] . ' 00:00:00';
                    // $endOfDay = $value[1] . ' 23:59:59';
                    // return $query->where($column, '>=', $startOfDay)->where($column, '<=', $endOfDay);

                    $startOfDay = date('Y-m-d', strtotime($value[0])) . ' 00:00:00';
                    $endOfDay = date('Y-m-d', strtotime($value[1])) . ' 23:59:59';
                    return $query->where($column, '>=', $startOfDay)->where($column, '<=', $endOfDay);

                    // return $query->whereDate($column, '>=', $value[0])->whereDate($column, '<=', $value[1]);
                }
            case 'in_date':
                $value = is_array($value) ? $value : explode(' to ', $value);
                // dd($value);
                if (count($value) === 1) {
                    // $startOfDay = $value[0] . ' 00:00:00';
                    // $endOfDay = $value[0] . ' 23:59:59';
                    // return $query->where($column, '>=', $startOfDay)->where($column, '<=', $endOfDay);
                    $startOfDay = date('Y-m-d', strtotime($value[0])) . ' 00:00:00';
                    $endOfDay = date('Y-m-d', strtotime($value[0])) . ' 23:59:59';
                    return $query->where($column, '>=', $startOfDay)->where($column, '<=', $endOfDay);
                } else {
                    // $startOfDay = $value[0] . ' 00:00:00';
                    // $endOfDay = $value[1] . ' 23:59:59';
                    // return $query->where($column, '>=', $startOfDay)->where($column, '<=', $endOfDay);

                    $startOfDay = date('Y-m-d', strtotime($value[0])) . ' 00:00:00';
                    $endOfDay = date('Y-m-d', strtotime($value[1])) . ' 23:59:59';
                    return $query->where($column, '>=', $startOfDay)->where($column, '<=', $endOfDay);

                    // return $query->whereDate($column, '>=', $value[0])->whereDate($column, '<=', $value[1]);
                }
            case 'exclusive_between_dates':
                $value = is_array($value) ? $value : explode(' to ', $value);
                if (count($value) === 1) {
                    $date = $value[0];
                    $currentDate = date('Y-m-d');
                    if ($date < $currentDate) {
                        return $query->where('exclusive_start', '<=', $date . ' 23:59:59');
                    } else {
                        return $query->where('exclusive_end', '>=', $date . ' 00:00:00');
                    }
                } else {
                    $startOfDay = $value[0] . ' 00:00:00';
                    $endOfDay = $value[1] . ' 23:59:59';
                    return $query->where('exclusive_start', '<=', $startOfDay)->where('exclusive_end', '>=', $endOfDay);
                }

            case 'today':
                return $query->whereDate($column, '=', now()->format('Y-m-d'));
            case 'yesterday':
                return $query->whereDate($column, '=', now()->subDay()->format('Y-m-d'));
            case 'last_month':
                $startOfMonth = now()->subMonth()->startOfMonth();
                $endOfMonth = now()->subMonth()->endOfMonth();
                return $query->whereBetween($column, [$startOfMonth, $endOfMonth]);
            case 'last_week':
                return $query->whereBetween($column, [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
            case 'this_month':
                return $query->whereYear($column, now()->year)->whereMonth($column, now()->month);
            case 'this_week':
                return $query->whereBetween($column, [now()->startOfWeek(), now()->endOfWeek()]);
            case 'whereJsonContains':
                return $query->whereJsonContains($column, $value);
            case 'whereJsonNotContains':
                return $query->whereJsonDoesntContain($column, $value);
            case 'hasCommunities':
                return $this->isArray($value)
                    ? $query->whereIn($column, $value)->where('parentable_type', 'App\Models\Community')
                    : $query->where($column, $value)->where('parentable_type', 'App\Models\Community');
            case 'hasNotCommunities':
                return $this->isArray($value)
                    ? $query->whereNotIn($column, $value)->where('parentable_type', 'App\Models\Community')
                    : $query->whereNot($column, $value)->where('parentable_type', 'App\Models\Community');
            case 'hasSubCommunities':
                return $this->isArray($value)
                    ? $query->whereIn($column, $value)->where(function ($q) use ($column, $value) {
                        $q->where('parentable_type', 'App\Models\SubCommunity')
                            ->orWhere('parentable_type', 'App\Models\CommunityProperty');
                    })
                    : $query->where($column, $value)->where(function ($q) use ($column, $value) {
                        $q->where('parentable_type', 'App\Models\SubCommunity')
                            ->orWhere('parentable_type', 'App\Models\CommunityProperty');
                    });
            case 'hasNotSubCommunities':
                return $this->isArray($value)
                    ? $query->whereNotIn($column, $value)->where(function ($q) use ($column, $value) {
                        $q->where('parentable_type', 'App\Models\SubCommunity')
                            ->orWhere('parentable_type', 'App\Models\CommunityProperty');
                    })
                    : $query->whereNot($column, $value)->where(function ($q) use ($column, $value) {
                        $q->where('parentable_type', 'App\Models\SubCommunity')
                            ->orWhere('parentable_type', 'App\Models\CommunityProperty');
                    });
            case 'hasCommunityProperty':
                return $this->isArray($value)
                    ? $query->whereIn($column, $value)->where('parentable_type', 'App\Models\CommunityProperty')
                    : $query->where($column, $value)->where('parentable_type', 'App\Models\CommunityProperty');
            case 'hasNotCommunityProperty':
                return $this->isArray($value)
                    ? $query->whereNotIn($column, $value)->where('parentable_type', 'App\Models\CommunityProperty')
                    : $query->whereNot($column, $value)->where('parentable_type', 'App\Models\CommunityProperty');
            case 'published':
                return $query->whereHas('propertycycles', function ($q) {
                    $q->where('status', 'PUBLISHED')->orderBy('created_at', 'desc')->limit(1);
                });
            case 'republished':
                return $query->whereHas('propertyCycles', function ($q) {
                    $q->where('status', 'PUBLISHED')
                        ->whereExists(function ($query) {
                            $query->select(DB::raw(1))
                                ->from('property_cycles as pc2')
                                ->whereColumn('properties.id', 'pc2.property_id')
                                ->where('pc2.status', 'UNPUBLISHED')
                                ->orderBy('pc2.created_at', 'desc')
                                ->limit(1);
                        });
                });

            default:

                throw new \InvalidArgumentException('Invalid operator: ' . $operator . ' for column: ' . $column . ' and value: ' . $value);
        }
    }
    private function handleCommunitiesFilter($query, $column, $value) {}

    private function applyPropertyFilters(Builder $builder, Request $request, $flag = false)
    {
        if(!isset($request->archived)){
            $request->archived = false;
        }
        if ($flag == true && $request->savefilter_name && $request->savefilter_name != '') {
            $updated_request = $request->except(['columns', 'draw', 'order', 'start', 'length']);
            $modifiable_keys = [
                'save_filter',
                'public_access',
                'save_filter_name',
                'search_property_build_up_area_from',
                'search_property_build_up_area_to',
                'search_property_build_up_area_operator',
                'search_property_build_up_area_to',
                'search_property_build_up_area_from',
                'search_property_plot_size_operator',
            ];

            $updated_request = array_combine(
                array_map(function ($key) use ($modifiable_keys) {
                    if (!in_array($key, $modifiable_keys)) {
                        return 'search_' . $key;
                    }
                    return $key;
                }, array_keys($updated_request)),
                $updated_request
            );

            $filterStore = Filter::create([
                'model' => Property::class,
                'user_id' => auth()->id(), // '0' is a dummy value, it will be updated to the actual id after the property is created
                'name' => $request->savefilter_name,
                'request' => json_encode($updated_request),
            ]);
        }

        try {

            $sub_communities = explode(',', $request->sub_communities);
            $properties = explode(',', $request->properties);
            $searchKeyWord = isset($request['search']) ? htmlspecialchars($request['search']['value']) : '';
            return $builder->when($request->managers, function ($q) use ($request) {
                $manager = User::where('id', \request('managers'))->first();
                $notAdmin = $manager->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
                $childrenIds = [];
                if ($notAdmin) {
                    $childrenIds = getUserChildrenIdsByID($request->managers);
                }
                $agents = User::whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
                })->when($notAdmin, function ($q) use ($childrenIds, $manager) {
                    $q->whereIn("parent_id", $childrenIds)
                        ->orWhere('id', $manager->id);
                })->when($manager->hasRole("Admin - Abu Dhabi"), fn($q) => $q->where("region_id", "2"))->get();
                return $this->queryBuilder($q, 'agent_id', \request('managers_operator'), $agents->pluck("id")->toArray());
            })
                ->when($request->agents || $this->getStatus($request->agents_operator), function ($q) {
                    return $this->queryBuilder($q, 'agent_id', \request('agents_operator'), \request('agents'));
                    // $this->isArray("agents") ? $q->whereIn("agent_id", \request("agents")) : $q->where("agent_id", \request("agents"));
                })
                ->when($request->id || $this->getStatus($request->id_operator), function ($q) {
                    return $this->queryBuilder($q, 'id', \request('id_operator'), \request('id'));
                })
                ->when($request->ref_no || $this->getStatus($request->ref_no_operator), function ($q) {
                    return $this->queryBuilder($q, 'ref_no', \request('ref_no_operator'), \request('ref_no'));
                })

                ->when($request->price_from && $request->price_to, function ($q) use ($request) {
                    $q->whereHas('price', function ($query) use ($request) {
                        $query->whereBetween('price', [$request->price_from, $request->price_to]);
                    });
                })
                ->when($request->price_from && !$request->price_to, function ($q) use ($request) {
                    $q->whereHas('price', function ($query) use ($request) {
                        $query->where('price', '>=', $request->price_from);
                    });
                })
                ->when($request->price_to && !$request->price_from, function ($q) use ($request) {
                    $q->whereHas('price', function ($query) use ($request) {
                        $query->where('price', '<=', $request->price_to);
                    });
                })

                ->when($request->owners || $this->getStatus($request->owners_operator), function ($q) {
                    return $this->queryBuilder($q, 'owner_id', \request('owners_operator'), \request('owners'));
                    // $this->isArray("owners") ? $q->whereIn("owner_id", \request("owners")) : $q->where("owner_id", \request("owners"));
                })
                ->when($request->refers || $this->getStatus($request->refer_operator), function ($q) {
                    return $this->queryBuilder($q, 'refered_by', \request('refer_operator'), \request('refers'));
                })
                ->when($request->published_date || $this->getStatus($request->published_date_operator), function ($q) {
                    if (\request('published_date_operator') == 'published') {
                        return $this->queryBuilder($q, 'published_date', 'between_dates', \request('published_date'))->whereNull('unpublished_date');
                    }
                    if (\request('published_date_operator') == 'republished') {
                        return $this->queryBuilder($q, 'republished_date', 'between_dates', \request('published_date'));
                    } else {
                        return $this->queryBuilder($q, 'unpublished_date', 'between_dates', \request('published_date'));
                    }
                })
                ->when($request->categories || $this->getStatus($request->categories_operator), function ($q) {
                    return $this->queryBuilder($q, 'property_category_id', \request('categories_operator'), \request('categories'));
                    // $this->isArray("categories") ? $q->whereIn("property_category_id", \request("categories")) : $q->where("property_category_id", \request("categories"));
                })
                ->when($request->type || $this->getStatus($request->type_operator), function ($q) {
                    return $this->queryBuilder($q, 'property_category_id', \request('type_operator'), \request('type'));
                    // $this->isArray("categories") ? $q->whereIn("property_category_id", \request("categories")) : $q->where("property_category_id", \request("categories"));
                })
                ->when($request->telesales || $this->getStatus($request->telesales_operator), function ($q) {
                    return $this->queryBuilder($q, 'telesales_id', \request('telesales_operator'), \request('telesales'));
                })->when($request->beds || $this->getStatus($request->beds_operator), function ($q, $request) {
                    return $this->queryBuilder($q, 'bed_id', \request('beds_operator'), \request('beds'));
                    // $this->isArray("beds") ? $q->whereIn("bed_id", \request("beds")) : $q->where("bed_id", \request("beds"));
                })->when($request->baths || $this->getStatus($request->baths_operator), function ($q) {
                    return $this->queryBuilder($q, 'bath_id', \request('baths_operator'), \request('baths'));

                    // $this->isArray("baths") ? $q->whereIn("bath_id", \request("baths")) : $q->where("bath_id", \request("baths"));
                })->when($request->property_request_statuses || $this->getStatus($request->property_request_statuses_operator), function ($q) {
                    return $this->queryBuilder($q, 'request_status_type', \request('property_request_statuses_operator'), \request('property_request_statuses'));
                })
                ->when($request->search_property_plot_size_from || $this->getStatus($request->search_property_plot_size_operator), function ($q) use ($request) {
                    $from = $request->input('search_property_plot_size_from', 0);
                    $to = $request->input('search_property_plot_size_to', 10000); // You can change the default value to whatever you want.
                    $range_ps = [$from, $to];
                    return $this->queryBuilder($q, 'plot_size', \request('search_property_plot_size_operator'), $range_ps);
                })
                ->when($request->search_property_build_up_area_from || $this->getStatus($request->search_property_build_up_area_operator), function ($q) use ($request) {

                    $from = $request->input('search_property_build_up_area_from', 0);
                    $to = $request->input('search_property_build_up_area_to', 10000); // You can change the default value to whatever you want.
                    $range = [$from, $to];

                    return $this->queryBuilder($q, 'built_up_area', \request('search_property_build_up_area_operator'), $range);
                })

                ->when($request->regions || $this->getStatus($request->regions_operator), function ($q) {
                    return $this->queryBuilder($q, 'region_id', \request('regions_operator'), \request('regions'));
                })

                ->when($request->unit_no || $this->getStatus($request->unit_no_operator), function ($q) {
                    return $this->queryBuilder($q, 'unit_no', \request('unit_no_operator'), \request('unit_no'));
                    // $q->where("unit_no", "LIKE", "%" .\request("unit_no") . "%");
                })->when($request->rera_permit_number || $this->getStatus($request->rera_permit_number_operator), function ($q) {
                    return $this->queryBuilder($q, 'rera_permit_number', \request('rera_permit_number_operator'), \request('rera_permit_number'));
                    // $q->where("rera_permit_number", "LIKE", "%" .\request("rera_permit_number") . "%");
                })->when($request->communities || $this->getStatus($request->communities_operator), function ($q) {
                    return $this->queryBuilder($q, 'community_id', \request('communities_operator'), \request('communities'));
                    // $this->isArray("communities") ? $q->whereIn("community_id", \request("communities")) : $q->where("community_id", \request("communities"));
                })->when($request->sub_communities, function ($q) use ($sub_communities) {
                    // return $this->queryBuilder($q, 'sub_community_id',\request('rera_permit_number_operator') , \request('sub_communities'));
                    is_array($sub_communities) ? $q->whereIn("sub_community_id", $sub_communities) : $q->where("sub_community_id", $sub_communities);
                })->when($request->properties, function ($q) use ($properties) {
                    // return $this->queryBuilder($q, 'rera_permit_number',\request('rera_permit_number_operator') , \request('rera_permit_number'));
                    is_array($properties) ? $q->whereIn("community_property_id", $properties) : $q->where("community_property_id", $properties);
                })->when($request->property_statuses || $this->getStatus($request->property_statuses_operator), function ($q) {
                    return $this->queryBuilder($q, 'property_status_id', \request('property_statuses_operator'), \request('property_statuses'));
                    // $this->isArray("property_statuses") ? $q->whereIn("property_status_id", \request("property_statuses")) : $q->where("property_status_id", \request("property_statuses"));
                })
                ->when($request->search_statuses || $this->getStatus($request->publish_statuses_operator), function ($q) {
                    return $this->queryBuilder($q, 'publish_status', \request('publish_statuses_operator'), \request('search_statuses'));
                })
                ->when($request->furnishing || $this->getStatus($request->furnishing_operator), function ($q) {

                    return $this->queryBuilder($q, 'furnished', \request('furnishing_operator'), \request('furnishing'));
                })
                ->when(!$request->search_statuses && !$request->archived, function ($q) {
                    $q->where("publish_status", "<>", "ARCHIVED");
                })
                ->when($request->is_live, function ($q) {
                    $q->where('is_live', true);
                })
                ->when($request->search_permit, function ($q) use ($request) {
                    $searchPermits = is_array($request->search_permit) ? $request->search_permit : [$request->search_permit];
                    $q->where(function ($q) use ($searchPermits) {
                        foreach ($searchPermits as $permit) {
                            switch ($permit) {
                                case 'EXPIRED':
                                    $q->orWhereDate('rera_expiry_date', '<', Carbon::today());
                                    break;
                                case 'VALID':
                                    $q->orWhereDate('rera_expiry_date', '>=', Carbon::today());
                                    break;
                                case 'EMPTY':
                                    $q->orWhereNull('rera_expiry_date');
                                    break;
                                case 'NOT_EMPTY':
                                    $q->orWhereNotNull('rera_expiry_date');
                                    break;
                                default:
                                    // Handle the default case as needed.
                                    break;
                            }
                        }
                    });
                })
                ->when($request->search_contract_dates || $this->getStatus($request->search_contract_dates_operator), function ($q) use ($request) {
                    return $this->queryBuilder($q, 'contract_start', \request('search_contract_dates_operator'), \request('search_contract_dates'));
                })

                ->when($request->for || $this->getStatus($request->for_operator), function ($q) {
                    return $this->queryBuilder($q, 'for', \request('for_operator'), \request('for'));

                    // $this->isArray("for") ? $q->whereIn("for", \request ("for")) : $q->where("for", \request ("for"));
                })
                ->when($request->portals || $this->getStatus($request->portals_operator), function ($q) use ($request) {
                    $q->whereHas("portals", function ($q) use ($request) {
                        $q->where(function ($q) use ($request) {
                            return $this->queryBuilder($q, 'portal_id', $request->portals_operator, $request->portals);
                        });
                    });
                })

                ->when($request->completion_status != "" || $this->getStatus($request->completion_status_operator), function ($q) {
                    return $this->queryBuilder($q, 'completion_status', \request('completion_status_operator'), \request('completion_status'));
                })->when($request->state != "" || $this->getStatus($request->state_operator), function ($q) {
                    return $this->queryBuilder($q, 'state', \request('state_operator'), \request('state'));
                    // $q->where("state", \request("state"));
                })->when($searchKeyWord != '', function ($q) use ($searchKeyWord) {
                    $q->where(function ($q) use ($searchKeyWord) {
                        $q->where('ref_no', 'LIKE', '%' . $searchKeyWord . '%')
                            ->orWhere('unit_no', 'LIKE', '%' . $searchKeyWord . '%')
                            ->orWhereHas('community', function ($q) use ($searchKeyWord) {
                                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                            })
                            ->orWhereHas('subCommunity', function ($q) use ($searchKeyWord) {
                                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                            })
                            ->orWhereHas('communityProperty', function ($q) use ($searchKeyWord) {
                                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                            });
                    });
                })->when($request->created_from || $this->getStatus($request->created_from_operator), function ($q) use ($request) {
                    return $this->queryBuilder($q, 'properties.created_at', \request('created_from_operator'), \request('created_from'));
                })
                ->when($request->updated_by || $this->getStatus($request->updated_by_operator), function ($q) use ($request) {
                    return $this->queryBuilder($q, 'updated_by_id', \request('updated_by_operator'), \request('updated_by'));
                })
                ->when($request->updated_at || $this->getStatus($request->updated_at_operator), function ($q) use ($request) {
                    return $this->queryBuilder($q, 'properties.updated_at', \request('updated_at_operator'), \request('updated_at'));
                })
                ->when(($request->exclusive_start || $this->getStatus($request->exclusive_start_operator)) && ($request->exclusive_end || $this->getStatus($request->exclusive_end_operator)), function ($q) use ($request) {

                    $start_datavalue = is_array($request->exclusive_start) ? $request->exclusive_start : explode(' to ', $request->exclusive_start);
                    $end_datavalue = is_array($request->exclusive_end) ? $request->exclusive_end : explode(' to ', $request->exclusive_end);

                    if (count($start_datavalue) === 1 && count($end_datavalue) === 1) {
                        // Case 1: Both are single dates
                        $startOfDay = $start_datavalue[0] . ' 00:00:00';
                        $endOfDay = $end_datavalue[0] . ' 23:59:59';
                        $q->where('exclusive_start', '>=', $startOfDay)
                            ->where('exclusive_end', '<=', $endOfDay);
                    } elseif (count($start_datavalue) > 1 && count($end_datavalue) === 1) {
                        // Case 2: start_datavalue is a range, and end_datavalue is a single date

                        $startOfDay = $start_datavalue[0] . ' 00:00:00';
                        $endOfDay = $start_datavalue[1] . ' 23:59:59';
                        // dd($startOfDay, $endOfDay, $end_datavalue[0]);
                        $q->whereBetween('exclusive_start', [$startOfDay, $endOfDay])->where('exclusive_end', '<=', $end_datavalue[0] . ' 00:00:00');
                    } elseif (count($start_datavalue) === 1 && count($end_datavalue) > 1) {
                        // Case 3: start_datavalue is a single date, and end_datavalue is a range
                        $startOfDay = $start_datavalue[0] . ' 00:00:00';
                        $endOfDay = $end_datavalue[1] . ' 23:59:59';
                        $q->where('exclusive_start', '>=', $startOfDay)
                            ->where('exclusive_end', '<=', $endOfDay)
                            ->where('exclusive_start', '>=', $start_datavalue[0]);
                    } elseif (count($start_datavalue) > 1 && count($end_datavalue) > 1) {
                        // Case 4: Both start_datavalue and end_datavalue are ranges
                        $startOfDay = $start_datavalue[0] . ' 00:00:00';
                        $endOfDay = $end_datavalue[1] . ' 23:59:59';
                        $q->where('exclusive_start', '>=', $startOfDay)
                            ->where('exclusive_start', '<=', $endOfDay)
                            ->where('exclusive_end', '>=', $start_datavalue[0])
                            ->where('exclusive_end', '<=', $end_datavalue[1]);
                    }
                })
                ->when(($request->exclusive_start || $this->getStatus($request->exclusive_start_operator)) && !($request->exclusive_end || $this->getStatus($request->exclusive_end_operator)), function ($q) use ($request) {
                    return $this->queryBuilder($q, 'properties.exclusive_start', \request('exclusive_start_operator'), \request('exclusive_start'));
                })
                ->when(!($request->exclusive_start || $this->getStatus($request->exclusive_start_operator)) && ($request->exclusive_end || $this->getStatus($request->exclusive_end_operator)), function ($q) use ($request) {
                    return $this->queryBuilder($q, 'properties.exclusive_end', \request('exclusive_end_operator'), \request('exclusive_end'));
                })
                // ->when($request->exclusive_start || $this->getStatus($request->exclusive_start_operator), function ($q) use ($request) {
                //     return $this->queryBuilder($q, 'properties.exclusive_start', \request('exclusive_start_operator'), \request('exclusive_start'));
                // })
                // ->when($request->exclusive_end || $this->getStatus($request->exclusive_end_operator), function ($q) use ($request) {
                //     return $this->queryBuilder($q, 'properties.exclusive_end', \request('exclusive_end_operator'), \request('exclusive_end'));
                // })
                ->when($request->next_available_from || $this->getStatus($request->next_available_from_operator), function ($q) use ($request) {
                    return $this->queryBuilder($q, 'next_available', \request('next_available_from_operator'), \request('next_available_from'));
                })
                // ->when($request->published_date || $this->getStatus($request->published_date_operator), function ($q) {
                //     if (\request('published_date_operator') == 'published') {
                //         return $q->whereHas('firstpublish', function ($q) {
                //             return $this->queryBuilder($q, 'created_at', 'in_date', \request('published_date'));
                //         });
                //     } else {
                //         return $q->whereHas('lastUnpublished', function ($q) {
                //             return $this->queryBuilder($q, 'created_at', 'in_date', \request('published_date'));
                //         });
                //         // return $this->queryBuilder($q, 'created_at', \request('published_date_operator'), \request('published_date'));
                //     }

                // })
                ->when($request->rented_until || $this->getStatus($request->rented_until_operator), function ($q) {
                    return $this->queryBuilder($q, 'rented_until', \request('rented_until_operator'), \request('rented_until'));
                })
                ->when($request->rera_start || $this->getStatus($request->rera_start_operator), function ($q) {
                    return $this->queryBuilder($q, 'rera_issue_date', \request('rera_start_operator'), \request('rera_start'));
                })
                ->when($request->rera_end || $this->getStatus($request->rera_end_operator), function ($q) {
                    return $this->queryBuilder($q, 'rera_expiry_date', \request('rera_end_operator'), \request('rera_end'));
                })
                ->when($request->boolean("hot"), function ($q) {
                    $q->where("hot", \request()->boolean("hot"));
                })
                ->when($request->boolean("refered"), function ($q) {
                    $q->whereNotNull("refered_by");
                })
                ->when($request->boolean("search_without_contract"), function ($q) {
                    $q->whereNull("contract_start");
                    $q->whereNull("contract_end");
                })
                ->when($request->boolean("deleted"), function ($q) {
                    $q->withTrashed();
                })
                ->when($request->boolean("exclusive"), function ($q) {
                    $q->where("exclusive", 1);
                })
                ->when($request->boolean("need_to_archive"), function ($q) {
                    $q->where("need_to_archive", 1);
                })
                ->when($request->boolean("republish"), function ($q) {
                    $q->where('ref_no', 'LIKE', 'axc-%-r%');
                })
            ;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    private function applyLeadFilters(Builder $builder, Request $request)
    {
        // dd($request->all());
        $superAdmin = auth()->user()->hasRole(['Super Admin']);

        $searchKeyWord = '';
        if (isset($request['search']['value'])) {

            $searchKeyWord = htmlspecialchars($request['search']['value']);
        }

        return $builder->when($request->managers, function ($q) use ($request) {
            $manager = User::where('id', \request('managers'))->first();
            $notAdmin = $manager->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIdsByID($request->managers);
            }
            $agents = User::where('status', 1)->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
            })
                ->when($notAdmin, function ($q) use ($childrenIds, $manager) {
                    $q->whereIn("parent_id", $childrenIds)->orWhere('id', $manager->id);
                })
                ->when($manager->hasRole("Admin - Abu Dhabi"), fn($q) => $q->where("region_id", "2"))->get();
            return $this->queryBuilder($q, 'agent_id', \request('managers_operator'), $agents->pluck("id")->toArray());
        })
            ->when($request->agents, function ($q) {
                return $this->queryBuilder($q, 'agent_id', \request('agents_operator'), \request('agents'));
            })
            ->when($request->all_listings == "true" && (auth()->user()->hasPermissionTo('Lead Pool List') || auth()->user()->hasRole(["Super Admin", "Admin", "Head of Secondary"])), function ($q) {
                if (auth()->user()->hasRole(["Agent"])) {
                    $threeHoursAgo = Carbon::now()->subHours(3);
                    $q->whereNull("agent_id")
                        ->where('portal_lead', false)
                        ->whereNull('converted_to_listing_lead')
                        ->where('need_to_be_destroyed', 0)
                        ->whereDate('created_at', '<', $threeHoursAgo->toDateString());
                } else {
                    $q->whereNull("agent_id")
                        ->where('portal_lead', false)
                        ->whereNull('converted_to_listing_lead')
                        ->where('need_to_be_destroyed', 0)
                    ;
                }
            })
            ->when($request->all_listings == "false" && $request->portal_lead == "false", function ($q) {
                $q->whereNotNull('agent_id');
            })
            ->when($request->portal_lead == "true", function ($q) {
                $q->where('portal_lead', 1);
            })
            ->when($request->communities, function ($q) {
                $q->whereHas("criteria", function ($q) {
                    $q->whereHas('locations', function ($q) {
                        return $this->queryBuilder($q, 'parentable_id', \request('communities_operator'), \request('communities'));
                    });
                });
            })->when($request->sub_communities, function ($q) {
                // dd(\request('sub_communities_operator'));
                $q->whereHas("criteria", function ($q) {
                    $q->whereHas('locations', function ($q) {
                        return $this->queryBuilder($q, 'parentable_id', \request('sub_communities_operator'), \request('sub_communities'));
                    });
                });
            })

            ->when($request->min_bed_id, function ($q) {
                $q->whereHas("criteria", function ($q) {
                    $this->isArray("min_bed_id") ? $q->whereIn('min_bed_id', \request("min_bed_id")) : $q->where('min_bed_id', \request("min_bed_id"));
                });
            })
            ->when($request->max_bed_id, function ($q) {
                $q->whereHas("criteria", function ($q) {
                    $this->isArray("max_bed_id") ? $q->whereIn('max_bed_id', \request("max_bed_id")) : $q->where('max_bed_id', \request("max_bed_id"));
                });
            })
            ->when($request->min_bath_id, function ($q) {
                $q->whereHas("criteria", function ($q) {
                    $this->isArray("min_bath_id") ? $q->whereIn('min_bath_id', \request("min_bath_id")) : $q->where('min_bath_id', \request("min_bath_id"));
                });
            })

            ->when($request->max_bath_id, function ($q) {
                $q->whereHas("criteria", function ($q) {
                    $this->isArray("max_bath_id") ? $q->whereIn('max_bath_id', \request("max_bath_id")) : $q->where('max_bath_id', \request("max_bath_id"));
                });
            })
            ->when($request->min_price, function ($q) {

                $q->whereHas("criteria", function ($q) {
                    $this->isArray("min_price") ? $q->whereIn('min_price', preg_replace('/[^0-9]/', '', \request("min_price"))) : $q->where('min_price', preg_replace('/[^0-9]/', '', \request("min_price")));
                });
            })
            ->when($request->max_price, function ($q) {
                $q->whereHas("criteria", function ($q) {

                    $this->isArray("max_price") ? $q->whereIn('max_price', preg_replace('/[^0-9]/', '', \request("max_price"))) : $q->where('max_price', preg_replace('/[^0-9]/', '', \request("max_price")));
                });
            })

            ->when($request->furnished || $this->getStatus($request->filter_furnished_operator), function ($q) {
                $q->whereHas("criteria", function ($q) {
                    return $this->queryBuilder($q, 'furnished', \request('filter_furnished_operator'), \request('furnished'));
                });
            })
            ->when($request->bitrix_data || $this->getStatus($request->bitrix_data_operator), function ($q) {
                $q->whereHas('criteria', function ($q) {
                    return $this->queryBuilder($q, 'bitrix_data', \request('bitrix_data_operator'), \request('bitrix_data'));
                    // $q->where('bitrix_data', 'LIKE', '%' . \request ("bitrix_data") . '%');
                });
            })
            ->when($request->for || $this->getStatus($request->for_operator), function ($q) use ($request) {
                return $this->queryBuilder($q, 'for', \request('for_operator'), \request('for'), true);
            })
            ->when($request->first_name || $this->getStatus($request->search_first_name_operator), function ($q) {
                return $this->queryBuilder($q, 'first_name', \request('search_first_name_operator'), \request('first_name'));
            })
            ->when($request->email || $this->getStatus($request->search_email_operator), function ($q) {
                return $this->queryBuilder($q, 'email', \request('search_email_operator'), \request('email'));
            })
            ->when($request->last_name || $this->getStatus($request->search_last_name_operator), function ($q) {
                return $this->queryBuilder($q, 'last_name', \request('search_last_name_operator'), \request('last_name'));
            })
            ->when($request->id || $this->getStatus($request->id_operator), function ($q) {
                return $this->queryBuilder($q, 'id', \request('id_operator'), \request('id'));
            })
            ->when($request->property || $this->getStatus($request->property_operator), function ($q) {
                return $this->queryBuilder($q, 'property_id', \request('property_operator'), \request('property'));
            })
            ->when($request->regions || $this->getStatus($request->regions_operator), function ($q) {
                // $q->whereHas("property", function ($q) {
                return $this->queryBuilder($q, 'region_id', \request('regions_operator'), \request('regions'));
                // });
            })
            ->when($request->completion_status || $this->getStatus($request->completion_status_operator), function ($q) {
                $q->whereHas("property", function ($q) {
                    return $this->queryBuilder($q, 'completion_status', \request('completion_status_operator'), \request('completion_status'));
                });
            })
            ->when($request->lead_statuses || $this->getStatus($request->lead_statuses_operator), function ($q) {
                return $this->queryBuilder($q, 'lead_status_id', \request('lead_statuses_operator'), \request('lead_statuses'));
            })
            ->when($request->previous_agent || $this->getStatus($request->previous_agent_operator), function ($q) {
                return $this->queryBuilder($q, 'previous_agent_id', \request('previous_agent_operator'), \request('previous_agent'));
            })
            ->when($request->updated_by || $this->getStatus($request->updated_by_operator), function ($q) {
                return $this->queryBuilder($q, 'updated_by_id', \request('updated_by_operator'), \request('updated_by'));
            })
            ->when($request->lead_medium || $this->getStatus($request->lead_medium_operator), function ($q) {
                return $this->queryBuilder($q, 'lead_medium_id', \request('lead_medium_operator'), \request('lead_medium'));
            })
            ->when($request->mobile || $this->getStatus($request->mobile_operator), function ($q) {
                return $this->queryBuilder($q, 'mobile', \request('mobile_operator'), \request('mobile'));
            })
            ->when($request->nationality || $this->getStatus($request->nationalities_operator), function ($q) {
                return $this->queryBuilder($q, 'nationality_id', \request('nationalities_operator'), \request('nationality'));
            })
            ->when($request->telesales || $this->getStatus($request->telesales_operator), function ($q) {
                return $this->queryBuilder($q, 'telesales_id', \request('telesales_operator'), \request('telesales'));
            })->when($request->telesales_head || $this->getStatus($request->telesales_head_operator), function ($q) {
                return $this->queryBuilder($q, 'telesales_head_id', \request('telesales_head_operator'), \request('telesales_head'));
            })
            ->when($request->priorities || $this->getStatus($request->priorities_operator), function ($q) {
                return $this->queryBuilder($q, 'priority', \request('priorities_operator'), \request('priorities'));
            })->when($request->lead_types || $this->getStatus($request->lead_types_operator), function ($q) {
                return $this->queryBuilder($q, 'type_id', \request('lead_types_operator'), \request('lead_types'));
            })->when($request->genders || $this->getStatus($request->genders_operator), function ($q) {
                return $this->queryBuilder($q, 'gender', \request('genders_operator'), \request('genders'));
            })->when($request->lead_sources || $this->getStatus($request->lead_sources_operator), function ($q) {
                return $this->queryBuilder($q, 'source_id', \request('lead_sources_operator'), \request('lead_sources'));
            })
            ->when($searchKeyWord != '', function ($q) use ($searchKeyWord) {
                $q->where(function ($q) use ($searchKeyWord) {
                    $q->where('first_name', 'LIKE', '%' . $searchKeyWord . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $searchKeyWord . '%')
                        ->orWhere('id', 'LIKE', '%' . $searchKeyWord . '%')
                        ->orWhere('phone', 'LIKE', '%' . $searchKeyWord . '%')
                        ->orWhere('email', 'LIKE', '%' . $searchKeyWord . '%')
                        ->orWhere(DB::raw("concat(first_name, ' ', COALESCE(last_name, ''))"), 'LIKE', "%" . $searchKeyWord . "%")
                        ->orWhere('mobile', 'LIKE', '%' . $searchKeyWord . '%')
                        ->orWhere('whatsapp', 'LIKE', '%' . $searchKeyWord . '%')
                        ->orWhere('company', 'LIKE', '%' . $searchKeyWord . '%');
                });
            })
            ->when($request->follow_up_date || $this->getStatus($request->follow_up_date_operator), function ($q) {
                return $this->queryBuilder($q, 'follow_up_date', \request('follow_up_date_operator'), \request('follow_up_date'));
            })

            ->when($request->lead_category || $this->getStatus($request->lead_category_operator), function ($q) {
                $cat = \request('lead_category');
                $ope = \request('lead_category_operator');

                switch ($cat) {
                    case 'Commercial':
                        return $q->whereHas('agent', function ($q) {
                            $q->where('is_commercial', true);
                        });
                        break;
                    case 'Abu Dhabi':
                        return $q->where('region_id', 2);
                        break;
                    case 'Secondary':
                        return $q->whereHas('agent', function ($q) {
                            $q->where('region_id', 1);
                        });
                        break;
                    case 'Primary':
                        return $q->whereHas('agent', function ($q) {
                            $q->where('bitrix_role', 'LIKE', '%Primary%')
                                ->where('bitrix_role', 'not like', '%Secondary%')
                                ->where('bitrix_role', 'not like', '%Abu-Dhabi Team%')
                                ->where('bitrix_role', 'not like', '%Primary Admin%');
                        });
                        break;
                    case 'Chinese':
                        return $q->whereHas('agent', function ($q) {
                            $q->where('bitrix_role', 'LIKE', '%Chinese%');
                        });
                        break;
                    default:
                        // Handle the default case as needed.
                        break;
                }
            })

            ->when($request->created_from || $this->getStatus($request->date_created_operator), function ($q) {
                return $this->queryBuilder($q, 'created_at', \request('date_created_operator'), \request('created_from'));
            })
            ->when($request->updated_from || $this->getStatus($request->date_updated_operator), function ($q) {
                return $this->queryBuilder($q, 'updated_at', \request('date_updated_operator'), \request('updated_from'));
            })

            // ->when($request->boolean("archive") && $superAdmin, function ($q) {
            //     $q->where(function ($q) {
            //         $q->whereIn("is_archive", [0, 1])->orWhereIn("status", [0, 1]);
            //     });
            // }, function ($q) {
            //     $q->where("status", 1)->where('is_archive', 0);
            // })
            ->when($request->boolean("deleted"), function ($q) {
                $q->withTrashed();
            })
            ->when($request->only_archive == 'true', function ($q) {
                $q->where('is_archive', 1);
            })
            ->when($request->boolean("incomplete_lead"), function ($q) {
                $q->where(function ($query) {
                    $query->where('first_name', 'LIKE', '%new%')->orWhereNull('first_name');
                })
                    ->where('status', 1)
                    ->where('is_archive', 0)
                    ->whereNull('converted_to_listing_lead')
                    // ->whereNull('property_id')
                    ->whereDate('created_at', '>', '2024-01-15')
                    ->whereNotIn('lead_status_id', [4, 12]);
            })

        ;
    }

    private function applyUserFilters(Builder $builder, Request $request)
    {
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        return $builder->when($request->roles, function ($q) {
            $this->isArray("roles") ? $q->whereHas("roles", fn($q) => $q->whereIn("name", \request("roles"))) : $q->whereHas("roles", fn($q) => $q->where("name", \request("roles")));
        })->when($searchKeyWord != '', function ($q) use ($searchKeyWord) {
            $q->where(function ($q) use ($searchKeyWord) {
                $q->where('first_name', 'LIKE', '%' . $searchKeyWord . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $searchKeyWord . '%')
                    ->orWhere('id', 'LIKE', '%' . $searchKeyWord . '%')
                    ->orWhere('email', 'LIKE', '%' . $searchKeyWord . '%')
                    ->orWhere(DB::raw("concat(first_name, ' ', COALESCE(last_name, ''))"), 'LIKE', "%" . $searchKeyWord . "%")
                    ->orWhere('mobile', 'LIKE', '%' . $searchKeyWord . '%');
            });
        })
            ->when(!auth()->user()->hasRole("Super Admin"), function ($q) {
                $q->where(function ($q) {
                    $q->whereHas("roles", function ($q) {
                        $q->whereNotIn("name", ["Super Admin"]);
                    })->orWhereDoesntHave("roles");
                });
            })
            ->when($request->first_name, function ($q) {
                $keyword = \request("first_name");
                !empty($keyword) ? $q->where('first_name', 'LIKE', "%{$keyword}%") : $q->where("first_name", $keyword);
            })
            ->when($request->bitrix_role, function ($q) {
                $keywords = \request("bitrix_role");

                if (is_string($keywords)) {
                    $keywords = [$keywords];
                }

                // $q->whereIn("bitrix_role", $keyword)->orWhere('bitrix_role', $keyword);
                $q->where(function ($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        // Use REGEXP with optional spaces around commas
                        $query->orWhereRaw("CONCAT(',', REPLACE(bitrix_role, ' ', ''), ',') REGEXP ? ", ['[[:<:]]' . preg_quote(trim($keyword)) . '[[:>:]]']);
                    }
                });
            })
            ->when($request->last_name, function ($q) {
                $keyword = \request("last_name");
                !empty($keyword) ? $q->where('last_name', 'LIKE', "%{$keyword}%") : $q->where("last_name", $keyword);
            })
            ->when($request->mobile, function ($q) {
                $keyword = \request("mobile");
                !empty($keyword) ? $q->where('mobile', "LIKE", "%{$keyword}%") : null;
            })
            ->when(isset($request->status), function ($q) {
                $keyword = \request("status");
                $status = \request("status") == "true" ? 1 : 0;
                $q->where('status', $status);
            })
            ->when($request->email, function ($q) {
                $keyword = \request("email");
                !empty($keyword) ? $q->where('email', $keyword) : null;
            })
            ->when(isset($request->without_telegram_chatid), function ($q) {
                $q->whereNull('telegram_chatid');
            })
            ->when(isset($request->without_parent), function ($q) {
                $q->where(function ($q) {
                    $q->whereDoesntHave('parent')->OrWhereHas('parent', function ($q) {
                        $q->where('status', 0);
                    });
                });
            })
            ->when($request->parent_id, function ($q) {
                !empty(\request("parent_id")) ? $q->where('parent_id', \request("parent_id")) : null;
            })
            ->when($request->position, function ($q) {
                $keyword = \request("position");
                !empty($keyword) ? $q->where('position', $keyword) : null;
            })
            ->when($request->licence_rera, function ($q) {
                $keyword = \request("licence_rera");
                !empty($keyword) ? $q->where('license_no', $keyword) : null;
            })
            ->when($request->telegram_username, function ($q) {
                $keyword = \request("telegram_username");
                !empty($keyword) ? $q->where('telegram_username', $keyword) : null;
            })
            ->when($request->telegram_chatid, function ($q) {
                $keyword = \request("telegram_chatid");
                !empty($keyword) ? $q->where('telegram_chatid', $keyword) : null;
            })
            ->when($request->public_name, function ($q) {
                $keyword = \request("public_name");
                !empty($keyword) ? $q->where('pp_name', "LIKE", "%{$keyword}%") : null;
            })
            ->when($request->public_license_no, function ($q) {
                $keyword = \request("public_license_no");
                !empty($keyword) ? $q->where('pp_license_no', "LIKE", "%{$keyword}%") : null;
            })
            ->when($request->nationality_id, function ($q) {
                $this->isArray("nationality_id") ? $q->whereIn("nationality_id", \request("nationality_id")) : $q->where("nationality_id", \request("nationality_id"));
            })
            ->when($request->logged_in == "true", function ($q) {
                $q->whereNotNull("device_token");
            })
            ->when($request->logged_in == "false", function ($q) {
                $q->whereNull("device_token");
            })
            ->when($request->google_authenticator == "true", function ($q) {
                $q->whereNotNull("google2fa_secret");
            })
            ->when($request->google_authenticator == "false", function ($q) {
                $q->whereNull("google2fa_secret");
            })
            ->when($request->created_from != "", function ($q) {
                $q->whereDate("created_at", ">=", \request("created_from"));
            })->when($request->created_to != "", function ($q) {
                $q->whereDate("created_at", "<=", \request("created_to"));
            })->when($request->boolean("api_access"), function ($q) {
                $q->where('api_access', 1);
            })->when($request->boolean("deleted"), function ($q) {
                $q->withTrashed();
            })->when($request->telelog_entities, function ($q) {
                $this->isArray("telelog_entities") ? $q->whereIn("telelog_entity", \request("telelog_entities")) : $q->where("telelog_entity", \request("telelog_entities"));
            });
    }

    private function applyContactFilters(Builder $builder, Request $request)
    {
        //  dd($request->all());
        $searchKeyWord = '';
        if (isset($request['search']['value'])) {
            $searchKeyWord = htmlspecialchars($request['search']['value']);
        }

        return $builder->when($request->agents || $this->getStatus($request->agents_operator), function ($q) {
            return $this->queryBuilder($q, 'agent_id', \request('agents_operator'), \request('agents'));
        })->when($searchKeyWord, function ($q) use ($searchKeyWord) {
            $q->where(function ($q) use ($searchKeyWord) {
                $q->where('first_name', 'LIKE', '%' . $searchKeyWord . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $searchKeyWord . '%')
                    ->orWhere('id', 'LIKE', '%' . $searchKeyWord . '%')
                    ->orWhere('phone', 'LIKE', '%' . $searchKeyWord . '%')
                    ->orWhere('email', 'LIKE', '%' . $searchKeyWord . '%')
                    ->orWhere(DB::raw("concat(first_name, ' ', COALESCE(last_name, ''))"), 'LIKE', "%" . $searchKeyWord . "%")
                    ->orWhere('mobile', 'LIKE', '%' . $searchKeyWord . '%')
                    ->orWhere('whatsapp', 'LIKE', '%' . $searchKeyWord . '%')
                    ->orWhere('company', 'LIKE', '%' . $searchKeyWord . '%');
            });
        })
            ->when($request->contact_sources || $this->getStatus($request->contact_sources_operator), function ($q) {
                return $this->queryBuilder($q, 'contact_source_id', \request('contact_sources_operator'), \request('contact_sources'));
                // $this->isArray("contact_sources") ? $q->whereIn("contact_source_id", \request ("contact_sources")) : $q->where("contact_source_id", \request ("contact_sources"));
            })->when($request->contact_statuses || $this->getStatus($request->contact_statuses_operator), function ($q) {
                return $this->queryBuilder($q, 'contact_status_id', \request('contact_statuses_operator'), \request('contact_statuses'));
                // $this->isArray("contact_statuses") ? $q->whereIn("contact_status_id", \request ("contact_statuses")) : $q->where("contact_status_id", \request ("contact_statuses"));
            })->when($request->contact_types, function ($q) {
                $q->whereHas("contactTypes", function ($q1) {
                    // dd($q1, \request ("contact_types"), \request ("contact_types_operator"));
                    return $this->queryBuilder($q1, 'contact_type_id', \request('contact_types_operator'), \request('contact_types'));

                    // $this->isArray("contact_types") ? $q1->whereIn("contact_type_id", \request ("contact_types")) : $q1->where("contact_type_id", \request ("contact_types"));
                });
            })
            ->when($request->id || $this->getStatus($request->id_operator), function ($q) {
                return $this->queryBuilder($q, 'id', \request('id_operator'), \request('id'));
            })
            ->when($request->mobile || $this->getStatus($request->mobile_operator), function ($q) {
                return $this->queryBuilder($q, 'mobile', \request('mobile_operator'), \request('mobile'));
            })
            ->when($request->genders || $this->getStatus($request->genders_operator), function ($q) {
                return $this->queryBuilder($q, 'gender', \request('genders_operator'), \request('genders'));

                // $this->isArray("genders") ? $q->whereIn("gender", \request ("genders")) : $q->where("gender", \request ("genders"));
            })->when($request->created_date != "" || $this->getStatus($request->created_date_operator), function ($q) {
                return $this->queryBuilder($q, 'created_at', \request('created_date_operator'), \request('created_date'));
            })->when($request->updated_date || $this->getStatus($request->updated_date_operator), function ($q) use ($request) {
                return $this->queryBuilder($q, 'updated_at', \request('updated_date_operator'), \request('updated_date'));
            })->when($request->bitrix_id || $this->getStatus($request->bitrix_id_operator), function ($q) use ($request) {
                return $this->queryBuilder($q, 'bitrix_id', \request('bitrix_id_operator'), \request('bitrix_id'));
            })->when($request->date_of_birth || $this->getStatus($request->date_of_birth_operator), function ($q) use ($request) {
                return $this->queryBuilder($q, 'dob', \request('date_of_birth_operator'), \request('date_of_birth'));
            })->when($request->my_crm_id || $this->getStatus($request->my_crm_id_operator), function ($q) use ($request) {
                return $this->queryBuilder($q, 'dob', \request('my_crm_id_operator'), \request('my_crm_id'));
            })
            ->when($request->boolean("deleted"), function ($q) {
                $q->withTrashed();
            });
    }

    private function applyDealFilters(Builder $builder, Request $request)
    {
        return $builder->when($request->agents, function ($q) {
            $this->isArray("agents") ? $q->whereIn("agent_id", \request("agents")) : $q->where("agent_id", \request("agents"));
        })->when($request->deal_types, function ($q) {
            $this->isArray("deal_types") ? $q->whereIn("type", \request("deal_types")) : $q->where("type", \request("deal_types"));
        })->when($request->transaction_types, function ($q) {
            $this->isArray("transaction_types") ? $q->whereIn("transaction_type_id", \request("transaction_types")) : $q->where("transaction_type_id", \request("transaction_types"));
        })->when($request->leads, function ($q) {
            $this->isArray("leads") ? $q->whereIn("lead_id", \request("leads")) : $q->where("lead_id", \request("leads"));
        })->when($request->contacts, function ($q) {
            $this->isArray("contacts") ? $q->whereIn("contact_id", \request("contacts")) : $q->where("contact_id", \request("contacts"));
        })->when($request->deal_sources, function ($q) {
            $this->isArray("deal_sources") ? $q->whereIn("deal_source_id", \request("deal_sources")) : $q->where("deal_source_id", \request("deal_sources"));
        })->when($request->deal_statuses, function ($q) {
            $this->isArray("statuses") ? $q->whereIn("status", \request("statuses")) : $q->where("status", \request("statuses"));
        })->when($request->deal_sub_statuses, function ($q) {
            $this->isArray("deal_sub_statuses") ? $q->whereIn("deal_sub_status_id", \request("deal_sub_statuses")) : $q->where("deal_sub_status_id", \request("deal_sub_statuses"));
        })->when($request->created_from != "", function ($q) {
            $q->whereDate("created_at", ">=", \request("created_from"));
        })->when($request->created_to != "", function ($q) {
            $q->whereDate("created_at", "<=", \request("created_to"));
        })->when($request->boolean("deleted"), function ($q) {
            $q->withTrashed();
        });
    }

    private function applyJobFilters(Builder $builder, Request $request)
    {
        return $builder->when($request->requested_by, function ($q) {
            $this->isArray("requested_by") ? $q->whereIn("requested_by_id", \request("requested_by")) : $q->where("requested_by_id", \request("requested_by"));
        })->when($request->types, function ($q) {
            $this->isArray("types") ? $q->whereIn("type", \request("types")) : $q->where("type", \request("types"));
        })->when($request->priorities, function ($q) {
            $this->isArray("priorities") ? $q->whereIn("priority", \request("priorities")) : $q->where("priority", \request("priorities"));
        })->when($request->assigned_to, function ($q) {
            $this->isArray("assigned_to") ? $q->whereIn("assigned_to_id", \request("assigned_to")) : $q->where("assigned_to_id", \request("assigned_to"));
        })->when($request->contacts, function ($q) {
            $this->isArray("approved_by") ? $q->whereIn("approved_by_id", \request("approved_by")) : $q->where("approved_by_id", \request("approved_by"));
        })->when($request->statuses, function ($q) {
            $this->isArray("statuses") ? $q->whereIn("job_status", \request("statuses")) : $q->where("job_status", \request("statuses"));
        })->when($request->created_from != "", function ($q) {
            $q->whereDate("created_at", ">=", \request("created_from"));
        })->when($request->created_to != "", function ($q) {
            $q->whereDate("created_at", "<=", \request("created_to"));
        })->when($request->boolean("deleted"), function ($q) {
            $q->withTrashed();
        });
    }

    private function applyShootJobFilters(Builder $builder, Request $request)
    {

        return $builder->when($request->requested_by, function ($q) {
            $this->isArray("requested_by") ? $q->whereIn("requested_by_id", \request("requested_by")) : $q->where("requested_by_id", \request("requested_by"));
        })->when($request->type != '', function ($q) {
            $type = \request("type") == 'Standard' ? '0' : '1';
            $this->isArray("type") ? $q->whereIn("is_luxury", $type) : $q->where("is_luxury", $type);
        })->when($request->assigned_to, function ($q) {
            $this->isArray("assigned_to") ? $q->whereIn("assigned_to_id", \request("assigned_to")) : $q->where("assigned_to_id", \request("assigned_to"));
        })->when($request->approved_by, function ($q) {
            $this->isArray("approved_by") ? $q->whereIn("approved_by_id", \request("approved_by")) : $q->where("approved_by_id", \request("approved_by"));
        })->when($request->rejected_by, function ($q) {
            $this->isArray("rejected_by") ? $q->whereIn("rejected_by_id", \request("rejected_by")) : $q->where("rejected_by_id", \request("rejected_by"));
        })->when($request->statuses, function ($q) {
            $this->isArray("statuses") ? $q->whereIn("status", \request("statuses")) : $q->where("status", \request("statuses"));
        })->when($request->created_from != "", function ($q) {
            $q->whereDate("created_at", ">=", \request("created_from"));
        })->when($request->created_to != "", function ($q) {
            $q->whereDate("created_at", "<=", \request("created_to"));
        })->when($request->boolean("deleted"), function ($q) {
            $q->withTrashed();
        });
    }

    private function applyLeadPoolFilters(Builder $builder, Request $request)
    {
        // dd($request->lead_statuses_operator);
        // info("manager_id", \request('manager_id'));
        $superAdmin = auth()->user()->hasRole(['Super Admin']);
        try {
            $sub_communities = explode(',', $request->sub_communities);
            // $community_properties = explode(',', $request->community_properties);

            $properties = explode(',', $request->properties);
            $searchKeyWord = '';
            if (isset($request['search']['value'])) {

                $searchKeyWord = htmlspecialchars($request['search']['value']);
            }
            // if (auth()->user()->hasRole('Team Leader')) {
            //     $builder->where('community_id', auth()->user()->community_id);
            // }

            if (auth()->user()->hasRole('Agent')) {
                $builder->where('agent_id', auth()->id());
            }
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->id());
            }
            return $builder->where(function ($builder) {
                $builder->where('status_id', '!=', 9)->orWhereNull('agent_id');
            })
                ->when(auth()->user()->hasRole('Agent'), function ($q) use ($childrenIds) {
                    $q->where(function ($q) use ($childrenIds) {
                        $q->whereIn('agent_id', $childrenIds);
                    });
                })
                ->when(auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]), function ($q) use ($childrenIds) {
                    $q->where(function ($q) use ($childrenIds) {
                        $q->whereIn('agent_id', $childrenIds)->orWhere(function ($q) {
                            $q->whereNull('agent_id')->where('status_id', 1);
                        });
                    });
                })
                // Restriction for locations here
                // ->when(!auth()->user()->hasRole(["Super Admin", "Admin", "Agent", "Telesales Head", "Telesales","Head of Secondary"]) && auth()->user()->hasPermissionTo("Listing Leads List"), function($q) {
                //     // Get the assigned communities and subcommunities for the current user
                //     $assignedCommunities = UserLocation::where('user_id', auth()->id())->where('parentable_type', 'App\Models\Community')->pluck('parentable_id');
                //     $assignedSubCommunities = UserLocation::where('user_id', auth()->id())->where('parentable_type', 'App\Models\SubCommunity')->pluck('parentable_id');

                //     // Get the unassigned communities and subcommunities
                //     $unassignedCommunities = Community::leftJoin('user_locations', function ($join) {
                //         $join->on('communities.id', '=', 'user_locations.parentable_id')
                //             ->where('user_locations.parentable_type', 'App\Models\Community')
                //             ->whereNull('user_locations.user_id');
                //     })->get();

                //     $unassignedSubCommunities = SubCommunity::leftJoin('user_locations', function ($join) {
                //         $join->on('sub_communities.id', '=', 'user_locations.parentable_id')
                //             ->where('user_locations.parentable_type', 'App\Models\SubCommunity')
                //             ->whereNull('user_locations.user_id');
                //     })->get();

                //     // Combine all arrays into a single array
                //     $allCommunities = $assignedCommunities->merge($unassignedCommunities->pluck('id'));
                //     $allSubCommunities = $assignedSubCommunities->merge($unassignedSubCommunities->pluck('id'));
                //     $q->where(function ($q) use ($allSubCommunities, $allCommunities) {
                //         $q->whereIn('sub_community_id', $allSubCommunities)
                //             ->orWhereIn('community_id', $allCommunities)
                //         ;
                //     });
                // })
                ->when($request->agent_id || $this->getStatus($request->agent_operator), function ($q) {
                    return $this->queryBuilder($q, 'agent_id', \request('agent_operator'), \request('agent_id'));
                })
                ->when($request->lead_statuses || $this->getStatus($request->lead_statuses_operator), function ($q) {

                    return $this->queryBuilder($q, 'status_id', \request('lead_statuses_operator'), \request('lead_statuses'));
                })

                ->when($request->manager_id || $this->getStatus($request->manager_operator), function ($q) {
                    return $this->queryBuilder($q, 'manager_id', \request('manager_operator'), \request('manager_id'));
                })

                ->when($request->sources || $this->getStatus($request->sources_operator), function ($q) {
                    return $this->queryBuilder($q, 'source_id', \request('sources_operator'), \request('sources'));
                })
                ->when($request->mobile || $this->getStatus($request->mobile_operator), function ($q) {
                    return $this->queryBuilder($q, 'mobile', \request('mobile_operator'), \request('mobile'));
                })
                ->when($request->unit_no || $this->getStatus($request->unit_no_operator), function ($q) {
                    return $this->queryBuilder($q, 'unit_no', \request('unit_no_operator'), \request('unit_no'));
                })
                ->when($request->property || $this->getStatus($request->property_operator), function ($q) {
                    return $this->queryBuilder($q, 'property_id', \request('property_operator'), \request('property'));
                })
                ->when($request->beds || $this->getStatus($request->beds_operator), function ($q) {
                    return $this->queryBuilder($q, 'bed_id', \request('beds_operator'), \request('beds'));
                })->when($request->baths || $this->getStatus($request->baths_operator), function ($q) {
                    return $this->queryBuilder($q, 'bath_id', \request('baths_operator'), \request('baths'));
                })->when($request->unit_no || $this->getStatus($request->unit_no_operator), function ($q) {
                    return $this->queryBuilder($q, 'unit_no', \request('unit_no_operator'), \request('unit_no'));
                })->when($request->communities || $this->getStatus($request->communities_operator), function ($q) {
                    return $this->queryBuilder($q, 'community_id', \request('communities_operator'), \request('communities'));
                })
                ->when($request->sub_communities, function ($q) {
                    is_array(\request('sub_communities')) ? $q->whereIn("sub_community_id", \request('sub_communities')) : $q->where("sub_community_id", \request('sub_communities'));
                })

                ->when($request->community_properties, function ($q) {
                    is_array(\request('community_properties')) ? $q->whereIn("community_property_id", \request('community_properties')) : $q->where("community_property_id", \request('community_properties'));
                })

                ->when($request->completion_status != "" || $this->getStatus($request->completion_status_operator), function ($q) {
                    return $this->queryBuilder($q, 'completion_status', \request('completion_status_operator'), \request('completion_status'));
                })->when($request->state != "" || $this->getStatus($request->state_operator), function ($q) {
                    return $this->queryBuilder($q, 'state', \request('state_operator'), \request('state'));
                })->when($searchKeyWord != '', function ($q) use ($searchKeyWord) {
                    $q->where(function ($q) use ($searchKeyWord) {

                        $q->where('unit_no', 'LIKE', '%' . $searchKeyWord . '%')
                            ->orWhereHas('subCommunity', function ($q) use ($searchKeyWord) {
                                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                            })
                            ->orWhereHas('communityProperty', function ($q) use ($searchKeyWord) {
                                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                            })
                            ->orWhere('id', 'LIKE', '%' . $searchKeyWord . '%')
                            ->orWhere(function ($query) use ($searchKeyWord) {
                                $query->whereRaw("CONCAT(first_name, ' ', COALESCE(last_name, '')) LIKE ?", ['%' . $searchKeyWord . '%']);
                            })
                            ->orWhere('mobile', 'LIKE', '%' . $searchKeyWord . '%')
                            ->orWhere('email', 'LIKE', '%' . $searchKeyWord . '%')
                        ;
                    });
                })->when($request->boolean("archive") && $superAdmin, function ($q) {
                    $q->where(function ($q) {
                        $q->whereIn("is_archive", [0, 1]);
                    });
                }, function ($q) {
                    $q->where('is_archive', 0);
                })

                ->when($request->created_from || $this->getStatus($request->created_from_operator), function ($q) use ($request) {
                    return $this->queryBuilder($q, 'created_at', \request('created_from_operator'), \request('created_from'));
                })->when($request->updated_from || $this->getStatus($request->updated_from_operator), function ($q) use ($request) {
                    return $this->queryBuilder($q, 'updated_at', \request('updated_from_operator'), \request('updated_from'));
                })->when($request->next_available_from || $this->getStatus($request->next_available_from_operator), function ($q) use ($request) {
                    return $this->queryBuilder($q, 'next_available', \request('next_available_from_operator'), \request('next_available_from'));
                })->when($request->boolean("deleted"), function ($q) {
                    $q->withTrashed();
                });
        } catch (\Throwable $th) {
            dd($th);
        }
    }
    private function applyRequestLogFilters(Builder $builder, Request $request)
    {

        return $builder->when($request->ip_address, function ($q) {
            $this->isArray("ip_address") ? $q->whereIn("ip_address", \request("ip_address")) : $q->where("ip_address", \request("ip_address"));
        })->when($request->user_id || $this->getStatus($request->user_operator), function ($q) {
            return $this->queryBuilder($q, 'user_id', \request('user_operator'), \request('user_id'));
        })->when($request->url, function ($q) {
            $this->isArray("url") ? $q->whereIn("url", \request("url")) : $q->where("url", \request("url"));
        })->when($request->method, function ($q) {
            $this->isArray("method") ? $q->whereIn("method", \request("method")) : $q->where("method", \request("method"));
        })->when($request->slug, function ($q) {
            $this->isArray("slug") ? $q->whereIn("slug", \request("slug")) : $q->where("slug", \request("slug"));
        })->when($request->response_code, function ($q) {
            $this->isArray("response_code") ? $q->whereIn("response_code", \request("response_code")) : $q->where("response_code", \request("response_code"));
        })->when($request->created_at != "" || $this->getStatus($request->created_date_operator), function ($q) {
            return $this->queryBuilder($q, 'created_at', \request('created_date_operator'), \request('created_at'));
        });
    }

    private function applyFolderMediaFilters(Builder $builder, Request $request)
    {
        $searchKeyWord = isset($request['search']) ? htmlspecialchars($request['search']['value']) : '';
        return $builder->when(!empty($request->type), function ($q) {
            $this->isArray("type") ? $q->whereIn("type", \request("type")) : $q->where("type", \request("type"));
        })->when(!empty($request->model_type), function ($q) {
            $q->whereHas("folder", function ($q1) {
                $modelType = "App\\Models\\" . request("model_type");
                $this->isArray("model_type") ? $q1->whereIn("model_type", \request("model_type")) : $q1->where("model_type", $modelType);
            });
        })->when($searchKeyWord != '', function ($q) use ($searchKeyWord) {
            $q->where(function ($q) use ($searchKeyWord) {
                $q->where('title', 'LIKE', '%' . $searchKeyWord . '%')
                    ->orWhere('type', 'LIKE', '%' . $searchKeyWord . '%')
                    ->orWhere('size', 'LIKE', '%' . $searchKeyWord . '%');
            });
        });
    }

    protected function applyPermitFilter($q, $permit)
    {
        switch ($permit) {
            case 'EXPIRED':
                $q->orWhere(function ($q) {
                    $q->whereDate('rera_expiry_date', '<', Carbon::today());
                });
                break;
            case 'VALID':
                $q->orWhere(function ($q) {
                    $q->whereDate('rera_expiry_date', '>=', Carbon::today());
                });
                break;
            case 'EMPTY':
                $q->orWhereNull('rera_expiry_date');
                break;
            case 'NOT_EMPTY':
                $q->orWhereNotNull('rera_expiry_date');
                break;
            default:
                break;
        }
    }
}
