<template>
    <div class="row">
        <div class="col-md-6 left-form" :style="{ maxHeight: '700px', overflowY: 'auto' }">
            <div class="row">
                <div class="col-12 text-center">
                    <h4>CONTRACT BETWEEN SELLER AND BROKER (A)</h4>

                </div>
                <div class="offset-1 col-3">
                    <label>Contract Number</label>

                </div>
                <div class="col-4">
                    <input type="text" class="form-control" v-model="formData.contract_number">
                </div>
                <div class="col-2">
                رقم العقد:
                </div>
                <div class="col-12">
                    <div class="fs-16 p-2" style="color: black !important;background: #abcaa1;">
                        THE BROKER OFFICE / AGENT DETAILS
                        <span class="float-end"></span>
                    </div>
                </div>
                <div class="col-12">

                    <div class="row mt-2" id="">
                        <div class="col-6">
                            <label for="agent_office_name" class="form-label">Office Name</label>
                            <input type="text" class="form-control" id="agent_office_name" v-model="formData.agent_office_name">
                        </div>
                        <div class="col-6">
                            <label for="agent_office_name_ar" class="form-label float-end">اسم الؤسسة:</label>
                            <input type="text" lang="ar" dir="rtl" class="form-control" id="agent_office_name_ar" v-model="formData.agent_office_name_ar">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-6">
                            <label for="agent_license_authority" class="form-label">License Authority</label>
                            <input type="text" class="form-control" id="agent_license_authority" v-model="formData.agent_license_authority">
                        </div>
                        <div class="col-6">
                            <label for="full_name" class="form-label float-end">جهة التخيص</label>
                            <input type="text" lang="ar" dir="rtl" class="form-control" id="full_name_ar" v-model="formData.agent_license_authority_ar">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-6">
                            <label for="full_name" class="form-label">ORN</label>
                            <input type="text" class="form-control" placeholder="رقم التسجيل" id="full_name" v-model="formData.agent_orn">
                        </div>
                        <div class="col-6">
                            <label for="full_name" class="form-label">License Number</label>
                            <input type="text"  class="form-control" placeholder="رقم التخيص" id="full_name_ar" v-model="formData.agent_license_number">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-6">
                            <label for="full_name" class="form-label">FAX</label>
                            <input type="text" class="form-control" placeholder="رقم الفاكس" id="full_name" v-model="formData.agent_fax">
                        </div>
                        <div class="col-6">
                            <label for="agent_phone" class="form-label">Phone</label>
                            <input type="text"  class="form-control" placeholder="رقم الهاتف" id="agent_phone" v-model="formData.agent_phone">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-6">
                            <label for="full_name" class="form-label">Address</label>
                            <input type="text" class="form-control" id="full_name" v-model="formData.agent_address">
                        </div>
                        <div class="col-6">
                            <label for="full_name" class="form-label float-end">العنوان</label>
                            <input type="text" lang="ar" dir="rtl" class="form-control" id="full_name_ar" v-model="formData.agent_address_ar">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-6">
                            <label for="full_name" class="form-label">Email</label>
                            <input type="text" class="form-control" id="email" v-model="formData.agent_email">
                        </div>
                        <div class="col-6">
                            <label for="full_name" class="form-label float-end">البيد اللكتوني</label>
                            <input type="text" lang="ar" dir="rtl" class="form-control" id="email_ar" v-model="formData.agent_email_ar">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-6">
                            <label for="agent_name" class="form-label">Agent Name</label>
                            <input type="text" class="form-control" id="agent_name" v-model="formData.agent_name">
                        </div>
                        <div class="col-6">
                            <label for="agent_name_ar" class="form-label float-end">اسم الوسيط</label>
                            <input type="text" lang="ar" dir="rtl" class="form-control" id="agent_name_ar" v-model="formData.agent_name_ar">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-6">
                            <label for="agent_brn" class="form-label">BRN</label>
                            <input type="text" class="form-control" id="agent_brn" v-model="formData.agent_brn">
                        </div>
                        <div class="col-6">
                            <label for="agent_mobile" class="form-label">Mobile</label>
                            <input type="text"  class="form-control" placeholder="رقم الهاتف الجوال" id="agent_mobile" v-model="formData.agent_mobile">
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="fs-16 p-2" style="color: black !important;background: #abcaa1;">
                            THE PROPERTY OWNERS DETAILS
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-6">
                            <label for="owner_name" class="form-label">Name Of Owner</label>
                            <input type="text" class="form-control" id="owner_name" v-model="formData.owner_name">
                        </div>
                        <div class="col-6">
                            <label for="owner_name_ar" class="form-label float-end">اسم مالك العقار</label>
                            <input type="text" lang="ar" dir="rtl" class="form-control" id="owner_name_ar" v-model="formData.owner_name_ar">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="id_card_number" class="form-label">ID Card Number</label>
                            <input type="text" class="form-control" placeholder="رقم بطاقة الهوية" id="id_card_number" v-model="formData.owner_id_card_number">
                        </div>
                        <div class="col-6">
                            <label for="owner_nationality" class="form-label float-end">Nationality</label>
                            <input type="text" lang="ar" dir="rtl" class="form-control" placeholder="الجنسية" id="owner_nationality" v-model="formData.owner_nationality">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="id_card_number" class="form-label">Passport No</label>
                            <input type="text" class="form-control" placeholder="رقم جواز السفر" id="id_card_number" v-model="formData.owner_passport_number">
                        </div>
                        <div class="col-6">
                            <label for="full_name_ar" class="form-label float-end">Expiry Date</label>
                            <input type="date" lang="ar" dir="rtl" class="form-control" placeholder="تاريخ النتهاء" id="passport_expiry_date" v-model="formData.owner_passport_expiry_date">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="id_card_number" class="form-label">Mobile</label>
                            <input type="text" class="form-control" placeholder="رقم الهاتف الجوال" id="id_card_number" v-model="formData.owner_mobile">
                        </div>
                        <div class="col-6">
                            <label for="full_name_ar" class="form-label float-end">P.O. Box</label>
                            <input type="text" lang="ar" dir="rtl" class="form-control" placeholder="صندوق البيد" id="passport_expiry_date" v-model="formData.owner_po_box">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="owner_phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" placeholder="الهاتف" id="owner_phone" v-model="formData.owner_phone">
                        </div>
                        <div class="col-6">
                            <label for="owner_fax" class="form-label float-end">Fax</label>
                            <input type="text" lang="ar" dir="rtl" class="form-control" placeholder="الفاكس" id="owner_fax" v-model="formData.owner_fax">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="owner_phone" class="form-label">Address</label>
                            <input type="text" class="form-control" placeholder="الهاتف" id="owner_phone" v-model="formData.owner_address">
                        </div>
                        <div class="col-6">
                            <label for="owner_address_ar" class="form-label float-end">العنوان</label>
                            <input type="text" lang="ar" dir="rtl" class="form-control" placeholder="الفاكس" id="owner_address_ar" v-model="formData.owner_address_ar">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="owner_email" class="form-label">Email</label>
                            <input type="text" class="form-control" placeholder="Email" id="owner_email" v-model="formData.owner_email">
                        </div>
                        <div class="col-6">
                            <label for="owner_email_ar" class="form-label float-end">البيد اللكتوني</label>
                            <input type="text" lang="ar" dir="rtl" class="form-control" placeholder="الفاكس" id="owner_email_ar" v-model="formData.owner_email_ar">
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="fs-16 p-2" style="color: black !important;background: #abcaa1;">
                            THE PROPERTY DETAILS
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="property_status" class="form-label">Property Status</label>
                            <input type="text" class="form-control" id="property_status" v-model="formData.property_status">
                        </div>
                        <div class="col-6">
                            <label for="property_plot_number" class="form-label">Plot Number</label>
                            <input type="text" class="form-control" id="property_plot_number" v-model="formData.property_plot_number">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="type_of_area" class="form-label">Type Of Area</label>
                            <input type="text" class="form-control" id="type_of_area" v-model="formData.type_of_area">
                        </div>
                        <div class="col-6">
                            <label for="title_deed_number" class="form-label">Title Deed Number</label>
                            <input type="text" class="form-control" id="title_deed_number" v-model="formData.title_deed_number">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="property_location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="property_location" v-model="formData.property_location">
                        </div>
                        <div class="col-6">
                            <label for="property_number" class="form-label">Property Number</label>
                            <input type="text" class="form-control" id="property_number" v-model="formData.property_number">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="property_type" class="form-label">Type Of Property</label>
                            <input type="text" class="form-control" id="property_type" v-model="formData.property_type">
                        </div>
                        <div class="col-6">
                            <label for="project_name" class="form-label">Project Name</label>
                            <input type="text" class="form-control" id="project_name" v-model="formData.project_name">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="property_area" class="form-label">Area</label>
                            <input type="text" class="form-control" id="property_area" v-model="formData.property_area">
                        </div>
                        <div class="col-6">
                            <label for="owner_association_no" class="form-label">Owners Association No</label>
                            <input type="text" class="form-control" id="owner_association_no" v-model="formData.owner_association_no">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="property_present_use" class="form-label">Present Use</label>
                            <input type="text" class="form-control" id="property_present_use" v-model="formData.property_present_use">
                        </div>
                        <div class="col-6">
                            <label for="community_number" class="form-label">Community Number</label>
                            <input type="text" class="form-control" id="community_number" v-model="formData.community_number">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="property_age" class="form-label">Property Approx Age</label>
                            <input type="text" class="form-control" id="property_age" v-model="formData.property_age">
                        </div>
                        <div class="col-6">

                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="car_parks" class="form-label">No. Of Car Parks</label>
                            <input type="text" class="form-control" id="car_parks" v-model="formData.car_parks">
                        </div>
                        <div class="col-6">
                            <label for="bedrooms" class="form-label ">No. Of Bedrooms</label>
                            <input type="text" class="form-control" id="bedrooms" v-model="formData.bedrooms">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="bathrooms" class="form-label">No. Of Bathrooms</label>
                            <input type="text" class="form-control" id="bathrooms" v-model="formData.bathrooms">
                        </div>
                        <div class="col-6">
                            <label for="bedrooms" class="form-label">No. Of Kitchens</label>
                            <input type="text" class="form-control" id="kitchens" v-model="formData.kitchens">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="unit_numbers" class="form-label">No. of Units / Unit Numbers</label>
                            <input type="text" class="form-control" id="unit_numbers" v-model="formData.unit_numbers">
                        </div>
                        <div class="col-6">
                            <label for="floor_no" class="form-label">Floor No.</label>
                            <input type="text" class="form-control" id="floor_no" v-model="formData.floor_no">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="no_of_floors" class="form-label">No. Of Floors</label>
                            <input type="text" class="form-control" id="no_of_floors" v-model="formData.no_of_floors">
                        </div>
                        <div class="col-6">
                            <label for="no_of_shops" class="form-label">Floor No.</label>
                            <input type="text" class="form-control" id="no_of_shops" v-model="formData.no_of_shops">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="no_of_floors" class="form-label">Facilities</label>
                            <input type="text" class="form-control" id="no_of_floors" v-model="formData.facilities">
                        </div>
                        <div class="col-6">
                            <label for="facilities_ar" class="form-label float-end">الخدمات</label>
                            <input type="text" lang="ar" dir="rtl" class="form-control" id="facilities_ar" v-model="formData.facilities_ar">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="no_of_floors" class="form-label">Extra Facilities</label>
                            <input type="text" class="form-control" id="no_of_floors" v-model="formData.extra_facilities">
                        </div>
                        <div class="col-6">
                            <label for="extra_facilities_ar" class="form-label float-end">الخدمات</label>
                            <input type="text" lang="ar" dir="rtl" class="form-control" id="extra_facilities_ar" v-model="formData.extra_facilities_ar">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="additional_information" class="form-label">Additional Information</label>
                            <input type="text" class="form-control" id="additional_information" v-model="formData.additional_information">
                        </div>
                        <div class="col-6">
                            <label for="additional_information_ar" class="form-label float-end">معلومات إضافية</label>
                            <input type="text" lang="ar" dir="rtl" class="form-control" id="additional_information_ar" v-model="formData.additional_information_ar">
                        </div>
                    </div>



                    <div class="col-12 mt-2">
                        <div class="fs-16 p-2" style="color: black !important;background: #abcaa1;">
                            Property Financials
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="listed_price" class="form-label">Listed Price</label>
                            <input type="text" class="form-control" id="listed_price" v-model="formData.listed_price">
                        </div>
                        <div class="col-6">
                            <label for="original_price" class="form-label">Plot Number</label>
                            <input type="text" class="form-control" id="original_price" v-model="formData.original_price">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="paid_amount" class="form-label">Paid Amount</label>
                            <input type="text" class="form-control" id="paid_amount" v-model="formData.paid_amount">
                        </div>
                        <div class="col-6">
                            <label for="balance_amount" class="form-label">Balance Amount</label>
                            <input type="text" class="form-control" id="balance_amount" v-model="formData.balance_amount">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="service_charge" class="form-label">Service Charge</label>
                            <input type="text" class="form-control" id="service_charge" v-model="formData.service_charge">
                        </div>
                        <div class="col-6">

                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="paid_amount" class="form-label">Mortgage Status</label>
                            <input type="text" class="form-control" id="paid_amount" v-model="formData.mortgage_status">
                        </div>
                        <div class="col-6">
                            <label for="mortgage_reg_no" class="form-label">Mortgage Registration No.</label>
                            <input type="text" class="form-control" id="mortgage_reg_no" v-model="formData.mortgage_reg_no">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="property_bank" class="form-label">Bank</label>
                            <input type="text" class="form-control" id="property_bank" v-model="formData.property_bank">
                        </div>
                        <div class="col-6">
                            <label for="mortgage_amount" class="form-label">Mortgage Amount</label>
                            <input type="text" class="form-control" id="mortgage_amount" v-model="formData.mortgage_amount">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="pre_closure_charge" class="form-label">Pre-Closure Charge</label>
                            <input type="text" class="form-control" id="pre_closure_charge" v-model="formData.pre_closure_charge">
                        </div>
                        <div class="col-6">
                            <label for="pre_closure_charge_ar" class="form-label float-end">البلغ الضاف الطلوب لفك الرهن</label>
                            <input type="text" class="form-control" dir="rtl" id="pre_closure_charge_ar" v-model="formData.pre_closure_charge_ar">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="payment_schedule" class="form-label">Payment Schedule</label>
                            <input type="text" class="form-control" id="payment_schedule" v-model="formData.payment_schedule">
                        </div>
                        <div class="col-6">
                            <label for="payment_schedule_ar" class="form-label float-end">جدول الدفعات</label>
                            <input type="text" class="form-control" dir="rtl" id="payment_schedule_ar" v-model="formData.payment_schedule_ar">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="payment_date1" class="form-label">Date التاريخ</label>
                            <input type="date" class="form-control" id="payment_date1" v-model="formData.payment_date1">
                            <input type="date" class="form-control" id="payment_date2" v-model="formData.payment_date2">
                        </div>
                        <div class="col-6">
                            <label for="amount_aed1" class="form-label float-end">Amount AED </label>
                            <input type="text" class="form-control"  id="amount_aed1" v-model="formData.amount_aed1">
                            <input type="text" class="form-control"  id="amount_aed2" v-model="formData.amount_aed2">
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="fs-16 p-2" style="color: black !important;background: #abcaa1;">
                           Tenancy Contract Details
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="is_rented" class="form-label">Is Property Rented</label>
                            <input type="text" class="form-control" id="is_rented" v-model="formData.is_rented">
                        </div>
                        <div class="col-6">
                            <label for="is_rented_ar" class="form-label float-end">هل العقار مؤجر؟ </label>
                            <input type="text" class="form-control" lang="ar" dir="rtl" id="is_rented_ar" v-model="formData.is_rented_ar">
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="fs-16 p-2" style="color: black !important;background: #abcaa1;">
                            PART 3 [COMMISSION & THE DURATION OF THE CONTRACT]
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="contract_start_date" class="form-label">Contract Start Date</label>
                            <input type="text" class="form-control" id="contract_start_date" v-model="formData.contract_start_date">
                        </div>
                        <div class="col-6">
                            <label for="contract_end_date" class="form-label float-end">Contract End Date </label>
                            <input type="text" class="form-control" id="contract_end_date" v-model="formData.contract_end_date">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="commission_amount" class="form-label">Commission Amount(AED)</label>
                            <input type="text" class="form-control" id="commission_amount" v-model="formData.commission_amount">
                        </div>
                        <div class="col-6">
                            <label for="commission_amount_ar" class="form-label float-end">نسبة العمولة (درهم) </label>
                            <input type="text" class="form-control" id="commission_amount_ar" v-model="formData.commission_amount_ar">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="contract_type" class="form-label">Contract Type</label>
                            <input type="text" class="form-control" id="contract_type" v-model="formData.contract_type">
                        </div>
                        <div class="col-6">
                            <label for="activity_reporting" class="form-label float-end">Activity Reporting</label>
                            <input type="text" class="form-control" id="activity_reporting" v-model="formData.activity_reporting">
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="fs-16 p-2" style="color: black !important;background: #abcaa1;">
                            FIRST PARTY [THE BROKER OFFICE]
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="broker_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="broker_name" v-model="formData.broker_name">
                        </div>
                        <div class="col-6">
                            <label for="broker_name_ar" class="form-label float-end">السم</label>
                            <input type="text" class="form-control" lang="ar" dir="rtl" id="broker_name_ar" v-model="formData.broker_name_ar">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="broker_title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="broker_title" v-model="formData.broker_title">
                        </div>
                        <div class="col-6">
                            <label for="broker_title_ar" class="form-label float-end">الصفة</label>
                            <input type="text" class="form-control" lang="ar" dir="rtl" id="broker_title_ar" v-model="formData.broker_title_ar">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="broker_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="broker_date" v-model="formData.broker_date">
                        </div>
                        <div class="col-6">

                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="fs-16 p-2" style="color: black !important;background: #abcaa1;">
                            SECOND PARTY [THE OWNER(S)] <span class="float-end">]الالك ]الثاني ا</span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="owner_name_en_1" class="form-label">Owner Name(English)</label>
                            <input type="text" class="form-control mt-1" id="owner_name_en_1" v-model="formData.owner_name_en_1">
                            <input type="text" class="form-control mt-1" id="owner_name_en_2" v-model="formData.owner_name_en_2">
                            <input type="text" class="form-control mt-1" id="owner_name_en_3" v-model="formData.owner_name_en_3">
                        </div>
                        <div class="col-6">
                            <label for="broker_name_ar" class="form-label float-end">Owner Name(Arabic)</label>
                            <input type="text" class="form-control mt-1" lang="ar" dir="rtl" id="owner_name_ar_1" v-model="formData.owner_name_ar_1">
                            <input type="text" class="form-control mt-1" lang="ar" dir="rtl" id="owner_name_ar_2" v-model="formData.owner_name_ar_2">
                            <input type="text" class="form-control mt-1" lang="ar" dir="rtl" id="owner_name_ar_3" v-model="formData.owner_name_ar_3">

                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-4">
                            <label for="legal_rep_name_en_1" class="form-label">Legal Representative (English)</label>
                            <input type="text" class="form-control mt-1" id="legal_rep_name_en_1" v-model="formData.legal_rep_name_en_1">
                            <input type="text" class="form-control mt-1" id="legal_rep_name_en_2" v-model="formData.legal_rep_name_en_2">
                            <input type="text" class="form-control mt-1" id="legal_rep_name_en_3" v-model="formData.legal_rep_name_en_3">
                        </div>
                        <div class="col-4">
                            <label for="legal_rep_name_ar_1" class="form-label float-end">Legal Representative (Arabic)</label>
                            <input type="text" class="form-control mt-1" lang="ar" dir="rtl" id="legal_rep_name_ar_1" v-model="formData.legal_rep_name_ar_1">
                            <input type="text" class="form-control mt-1" lang="ar" dir="rtl" id="legal_rep_name_ar_2" v-model="formData.legal_rep_name_ar_2">
                            <input type="text" class="form-control mt-1" lang="ar" dir="rtl" id="legal_rep_name_ar_3" v-model="formData.legal_rep_name_ar_3">

                        </div>
                        <div class="col-4">
                            <label for="attorney_number1" class="form-label">Attorney Number</label>
                            <input type="text" class="form-control mt-1" id="attorney_number1" v-model="formData.attorney_number1">
                            <input type="text" class="form-control mt-1" id="attorney_number2" v-model="formData.attorney_number2">
                            <input type="text" class="form-control mt-1" id="attorney_number3" v-model="formData.attorney_number3">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6" id="pdfIframeSection">
            <iframe id="pdfIFrame" :src="docURl" frameborder="0" class="responsive-iframe" width="100%"
                    height="700px"></iframe>
        </div>
    </div>
</template>
<script setup>
import {ref, defineExpose, onMounted, watch, computed, onBeforeMount} from 'vue';
import {useRoute} from 'vue-router';
import useGlobalHelper from "../../../../composables/helpers/global";
const {getCountries, countries} = useGlobalHelper();
const route = useRoute();
import useFolders from '../../composables/folders';
import useLeads from "../../../leads/composables/leads";

const {
    lead,
    getLead,
} = useLeads();
const {
    file,
    sourceFile,
    getFile,
} = useFolders();
const {generatePDF} = useFolders();
const props = defineProps({
    file: {
        type: Object || null,
        required: false
    },
    propsSourceFile: {
        type: Object || null,
        required: false
    }
});
const lead_id = computed(() => route.query.lead_id);
const meta = ref([]);
const emit = defineEmits(["setDownloadFileUrl"]);

//const docURl = ref(null)
const docURl = ref(`${documentsPath}/Form_A_contract_seller_broker.pdf`)

const formData = ref({
    file_id: '',
    filename: '',
    type: "form-a-contract-seller-broker",
    contract_number: '',
    agent_office_name: '',
    agent_office_name_ar: '',
    agent_license_authority: '',
    agent_license_authority_ar: '',
    agent_orn: '',
    agent_license_number: '',
    agent_fax: '',
    agent_phone: '',
    agent_address: '',
    agent_address_ar: '',
    agent_email: '',
    agent_email_ar: '',
    agent_name: '',
    agent_name_ar: '',
    agent_brn: '',
    agent_mobile: '',
    owner_name: '',
    owner_name_ar: '',
    owner_id_card_number: '',
    owner_nationality: '',
    owner_passport_number: '',
    owner_passport_expiry_date: '',
    owner_mobile: '',
    owner_po_box: '',
    owner_phone: '',
    owner_fax: '',
    owner_address: '',
    owner_address_ar: '',
    owner_email: '',
    owner_email_ar: '',
    property_status: '',
    property_plot_number: '',
    type_of_area: '',
    title_deed_number: '',
    property_location: '',
    property_number: '',
    property_type: '',
    project_name: '',
    property_area: '',
    owner_association_no: '',
    property_present_use: '',
    community_number: '',
    property_age: '',
    car_parks: '',
    bedrooms: '',
    bathrooms: '',
    kitchens: '',
    unit_numbers: '',
    floor_no: '',
    no_of_floors: '',
    no_of_shops: '',
    facilities: '',
    facilities_ar: '',
    extra_facilities: '',
    extra_facilities_ar: '',
    additional_information: '',
    additional_information_ar: '',
    listed_price: '',
    original_price: '',
    paid_amount: '',
    balance_amount: '',
    service_charge: '',
    mortgage_status: '',
    mortgage_reg_no: '',
    property_bank: '',
    mortgage_amount: '',
    pre_closure_charge: '',
    pre_closure_charge_ar: '',
    payment_schedule: '',
    payment_schedule_ar: '',
    payment_date1: '',
    payment_date2: '',
    amount_aed1: '',
    amount_aed2: '',
    is_rented: '',
    is_rented_ar: '',
    contract_start_date: '',
    contract_end_date: '',
    commission_amount: '',
    commission_amount_ar: '',
    contract_type: '',
    activity_reporting: '',
    broker_name: '',
broker_name_ar: '',
broker_title: '',
broker_title_ar: '',
broker_date: '',
    owner_name_en_1: '',
    owner_name_en_2: '',
    owner_name_en_3: '',
    owner_name_ar_1: '',
    owner_name_ar_2: '',
    owner_name_ar_3: '',
    legal_rep_name_en_1: '',
    legal_rep_name_en_2: '',
    legal_rep_name_en_3: '',
    legal_rep_name_ar_1: '',
    legal_rep_name_ar_2: '',
    legal_rep_name_ar_3: '',
    attorney_number1: '',
    attorney_number2: '',
    attorney_number3: '',

})

const refreshIFrame = () => {
    const iframe = document.getElementById('pdfIFrame');
    if (iframe) {
        const contentDoc = iframe.contentDocument;

        if (contentDoc) {
            contentDoc.location.reload(true);
        }
    }
}

const setFormData = (deal, file = null, reset = false) => {
    // if(deal === null || reset === true){
    //     Object.keys(formData.value).map(key => {
    //         if(key == "filename" || key == "type"){
    //             return;
    //         }
    //         formData.value[key] = '';
    //     })
    //     return
    // }
    if (file !== null && file !== undefined) {
        const {data} = file;
        if (file.path) {
            docURl.value = file.path;
            refreshIFrame(docURl.value);
        }
        if(data != null || data != undefined){
            console.log(data)
            Object.keys(data).map(key => {
                formData.value[key] = data[key] || '';
            })
        }

        formData.value.file_id = file.id;
        return;
    }

    formData.value.deal_id = deal.id || '';
    formData.value.property_usage = '';

}
const setIframe = (url) => {
    // Get the reference to the parent element
    const pdfIframeSection = document.getElementById('pdfIframeSection');
    pdfIframeSection.innerHTML = '';
// Remove any existing iframe element
    const existingIframe = pdfIframeSection.querySelector('iframe');
    if (existingIframe) {
        pdfIframeSection.removeChild(existingIframe);
    }

// Create a new iframe element
    const newIframe = document.createElement('iframe');
    newIframe.setAttribute('src', url);
    newIframe.setAttribute('width', '100%');
    newIframe.setAttribute('height', '700px');
    newIframe.setAttribute('frameborder', '0');
    newIframe.setAttribute('scrolling', 'no');
    newIframe.setAttribute('class', 'responsive-iframe');

// Append the new iframe to the parent element
    pdfIframeSection.appendChild(newIframe);

}
const submitForm = async () => {
    emit("setDownloadFileUrl", "");
    //  let url = 'https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/file-manager/pdf/lead-kyc-form-individual.pdf';

    const res = await generatePDF(formData.value);
    if (res && res.data && res.data.status === '1') {
        if (res.data.url) {
            docURl.value = res.data.url;
            // let iframeSection = document.getElementById('pdfIframeSection');
            // iframeSection.innerHTML = '';
            // iframeSection.innerHTML = `<iframe id="pdfIFrame" src="`+res.data.url+`" frameborder="0" class="responsive-iframe" width="100%"
            //         height="700px"></iframe>`;
            // iframeSection.scrollIntoView({behavior: "smooth"});
            setIframe(docURl.value);
            //    refreshIFrame();
            emit("setDownloadFileUrl", res.data.url);
        }
    }
}

defineExpose({
    submitForm
})
watch(() => file.value, (val) => {
    docURl.value = val.path;
    formData.value.file_id = val.id;
    refreshIFrame();
})
watch(() => sourceFile.value, (val) => {
    if(file.value !== null){
        docURl.value = file.value.path;
    }else if(val !== null) {
        docURl.value = val.path;
    }

})

const findNationality =(id) =>  {
    const foundCountry = countries.value.find(country => country.id === id);
    return foundCountry ? foundCountry.nationality : "";
}
onMounted(() => {


    formData.value.filename = moment(new Date()).format("YYYY-MM-DD H-m-ss");
    // getFile('', 'lead-kyc-form-individual','source')

})
onBeforeMount(() => {
    getCountries();

})
onMounted(() => {
    console.log('form a mounted')

    if (Object.keys(props.file || {}).length !== 0) {

        setFormData(null, props.file)
    }
})

</script>
<style scoped>

</style>
