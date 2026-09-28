import { ref } from 'vue'
import axios from 'axios'

export default function useDeals() {
    const data = ref({})
    const errors = ref('')
    const formStatus = ref('PENDING')
    const deal = ref({});
    const dealMedia = ref({});
    const leads = ref([])
    //make lead reactive
    const lead = ref({})
    const notes = ref([])
    const logs = ref([])
    const leadTypes = ref([]);
    const dealTasks = ref({});
    const searchedLeads = ref([]);
    const splits = ref([]);
    const splits_commission = ref([]);
    const commissions = ref([]);
    const dealCommissions = ref([]);
    const deductibleType = ref([]);
    const deductibles = ref([]);
    const modalClose = ref(false);
    const contact = ref([]);
    const property = ref({});

    const createDeal = async (data) => {
        try {
            const response = await axios.post('/api/vue/v1/create-deal', data);
            if (response.data.status) {
                const res = response.data.data;

                getDeal(res.id);
                showToastify('Deal created successfully', 'success');
            } else {
                showToastify('Something went wrong', 'error');
            }
            return response;
        } catch (error) {
            //handle 500 error
            if (error.response.status === 500) {
                showToastify('Something went wrong', 'error');
            }
            // Handle error
            console.error('Error creating lead:', error);
            return error;
        }

        // let response = await axios.post('/api/vue/v1/create-deal/', data).then(function(response){
        //     if(response.data.status){
        //         let res = response.data.data;
        //         getDeal(res.id);
        //         showToastify('Deal created successfully', 'success');
        //     }else{
        //         showToastify('Something went wrong', 'error')
        //     }
        //     return response;
        // }).catch(function(){
        //     return response;
        // });
    }
    const addAgentInvitation = async (data) => {
        try {
            const response = await axios.post('/api/vue/v1/generate-invite-link', data);
            if (response.data.status) {
                const res = response.data.data;
                showToastify('Invitation sent successfully', 'success');
            } else {
                showToastify('Something went wrong', 'error');
            }
            return response;
        } catch (error) {
            //handle 500 error
            if (error.response.status === 500) {
                showToastify('Something went wrong', 'error');
            }
            // Handle error
            console.error('Error creating lead:', error);
            return error;
        }

    }
    //owner related function
    const getContact = async (id) => {
        let response = await axios.get(route('admin.work.deals.get.contact')+`?id=${id}`)
        contact.value = response.data.data
    }
    const updateContact = async (data) => {
        let isItSuccess = false;
        errors.value = ''
        try {
            const config = {
                headers: {
                    'Content-type': 'multipart/form-data'
                }
            }
            let response = await axios.post('/api/vue/v1/update-owner', data).then(function(response){

                //get http response status
                let status = response.status;
                // console.log(response.status)
                // if(response.errors != null && response.errors != undefined && response.errors != ''){
                //     errors.value = response.errors;
                //     for (const key in response.errors) {
                //         showToastify(response.errors[key], 'error')
                //     }
                //     isItSuccess = false;
                // }
                if(response.data.status){
                    data.value = response.data.data;

                    showToastify('Owner details updated successfully', 'success');
                    isItSuccess = true;

                }else if(response.data.status =='2'){
                    console.log('here')
                    data.value = null;
                    errors.value = response.data.errors;
                    isItSuccess = false;
                    showToastify('Something went wrong', 'error')

                }else{
                    isItSuccess = false;
                    showToastify('Something went wrong', 'error')
                }
            }).catch(error => {
                if (error.response) {
                    if(error.response.status == 422) {
                        let datax = error.response.data;
                        errors.value = datax.errors;
                        data.value = null;
                        if (datax.errors) {
                            $.each(datax.errors, function (index, item) {
                                showToastify(item[0], "error")
                            })
                        }
                    }
                }
                isItSuccess = false;
            });

            // catch(function(jqXhr, textStatus, errorThrown){
            //     let data = jqXhr.responseJSON;
            //     console.log(data)
            //     console.log(errorThrown)
            //     // let data = jqXhr.responseJSON;
            //     // if (data.errors) {
            //     //     $.each(data.errors, function (index, item) {
            //     //         showToastify(item[0], "error")
            //     //     })
            //     // }
            //     if (jqXhr.status == 500 || jqXhr.status == 400) {
            //         showToastify(response.message, "success")
            //     }
            // });



        } catch (e) {
            isItSuccess = false;
            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value = e.response.data.errors
                }
            }
        }
        return { data, errors, updateContact };
    }
    const updateContactDocuments = async (data) => {
        let isItSuccess = false;
        errors.value = ''
        try {
            const config = {
                headers: {
                    'Content-type': 'multipart/form-data'
                }
            }
            let response = await axios.post('/api/vue/v1/update-owner-documents', data).then(function(response){
                let status = response.status;
                if(response.errors != null && response.errors != undefined && response.errors != ''){
                    errors.value = response.errors;
                    for (const key in response.errors) {
                        showToastify(response.errors[key], 'error')
                    }
                    getContact(data.id);
                    isItSuccess = false;
                }
                if(response.data.status){


                    showToastify(response.data.msg, 'success');
                    isItSuccess = true;

                }else{
                    isItSuccess = false;
                    showToastify('Something went wrong', 'error')
                }
            }).catch(error => {
                if (error.response) {
                    if(error.response.status == 422) {
                        let data = error.response.data;
                        if (data.errors) {
                            $.each(data.errors, function (index, item) {
                                showToastify(item[0], "error")
                            })
                        }
                    }
                }
                isItSuccess = false;
            });


        } catch (e) {
            isItSuccess = false;
            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value = e.response.data.errors
                }
            }
        }
        return isItSuccess;
    }
    const updateDealDocuments = async (data,deal_id) => {
        let isItSuccess = false;
        errors.value = ''
        try {
            const config = {
                headers: {
                    'Content-type': 'multipart/form-data'
                }
            }
            let response = await axios.post('/api/vue/v1/update-deal-doc', data).then(function (response) {
                let status = response.status;
                if (response.errors != null && response.errors != undefined && response.errors != '') {
                    errors.value = response.errors;
                    for (const key in response.errors) {
                        showToastify(response.errors[key], 'error')
                    }
                   // getDeal(data.id);
                    isItSuccess = false;
                }
                if (response.data.status) {
                  //  getDeal(data.id);
                    showToastify(response.data.msg, 'success');
                    isItSuccess = true;
                }
            }).catch(error => {
                if (error.response) {
                    if (error.response.status == 422) {
                        let data = error.response.data;
                        if (data.errors) {
                            $.each(data.errors, function (index, item) {
                                showToastify(item[0], "error")
                            })
                        }
                    }
                }
                isItSuccess = false;
            });
        } catch (e) {
            isItSuccess = false;
            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value = e.response.data.errors
                }
            }
        }

    }
    const getDealMedia = async (id) => {
        let response = await axios.get(`/api/vue/v1/get-deal-media/?deal_id=${id}`)
        dealMedia.value = response.data.data

    }
    const deleteDealMedia = async (data) => {
        let response = await axios.post(`/api/vue/v1/delete-deal-media`,data).then(function(response){
            if(response.data.status){
                getDealMedia(response.data.data.deal_id);
                showToastify('File removed successfully', 'success');
            }else{
                showToastify('Something went wrong', 'error')
            }
        }).catch(function(){
            //console.log('FAILURE!!');
        });
    }
    const getDealLeads = async (id) => {
        let response = await axios.get(`/api/vue/v1/get-deal-leads/?deal_id=${id}`)
        leads.value = response.data.data
        console.log(leads.value)
    }
    const getNotes = async (id) => {
        let response = await axios.get(`/api/vue/v1/get-deal-notes/?deal_id=${id}`)
        notes.value = response.data.data
    }
    const addDealNote = async (data,deal_id) => {
        errors.value = ''
        try {
            const config = {
                headers: {
                    'Content-type': 'multipart/form-data'
                }
            }
            let response = await axios.post('/api/vue/v1/add-deal-note', data).then(function (response) {

                if (response.data.status) {
                    getNotes(deal_id);
                    showToastify('Note added successfully', 'success');
                } else {
                    showToastify('Something went wrong', 'error')
                }
                return response;
            }).catch(function () {
                return response;
                //console.log('FAILURE!!');
            });

            return response;
        } catch (e) {
            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value = e.response.data.errors
                }
            }
            return e;
        }
    }
    const getActivityLog = async (id) => {
        let response = await axios.get(`/api/vue/v1/get-deal-activity-logs/?deal_id=${id}`)
        logs.value = response.data.data
    }
    const getLead = async (id) => {
        let response = await axios.get(`/api/vue/v1/get-lead/?id=${id}`)
        lead.value = response.data.data
    }
    const getDealTypes = async () => {
        let response = await axios.get(`/api/vue/v1/get-deal-type`)
        dealTypes.value = response.data.data
    }
    const getLeadTypes = async () => {
        let response = await axios.get(`/api/vue/v1/get-lead-types`)
        leadTypes.value = response.data.data
    }
    const getDeal = async (id) => {
        let response = await axios.get(`/api/vue/v1/get-deal/?id=${id}`)
        deal.value = response.data.data
    }
    const getDealTasks = async (deal_id,limit ='',status='') => {
        let response = await axios.get(`/api/vue/v1/get-deal-tasks/?deal_id=${deal_id}&limit=${limit}&status=${status}`)
        dealTasks.value = response.data.data
    }
    const storeDealTask = async (data) => {
        errors.value = ''
        try {
            const config = {
                headers: {
                    'Content-type': 'multipart/form-data'
                }
            }
            let response = await axios.post('/api/vue/v1/store-deal-tasks', data).then(function(response){

                if(response.data.status){
                    getDealTasks(data.deal_id);
                    showToastify('Task created successfully', 'success');
                }else{
                    showToastify('Something went wrong', 'error')
                }
                return response;
            }).catch(function(){
                return response;
                //console.log('FAILURE!!');
            });

            return response;


        } catch (e) {
            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value = e.response.data.errors
                }
            }
            return e;
        }

    }
    const updateLead = async (data,id,deal_id) => {
        let isItSuccess = false;
        errors.value = ''
        try {
            const config = {
                headers: {
                    'Content-type': 'multipart/form-data'
                }
            }
            let response = await axios.post('/api/vue/v1/update-lead', data).then(function(response){

                //get http response status
                let status = response.status;
               // console.log(response.status)
                if(response.errors != null && response.errors != undefined && response.errors != ''){
                    errors.value = response.errors;
                    for (const key in response.errors) {
                        showToastify(response.errors[key], 'error')
                    }
                    isItSuccess = false;
                }
                if(response.data.status){
                    formStatus.value = 'SUCCESS';
                    getLead(id);
                   // getDealLeads(deal_id);

                    showToastify('Lead updated successfully', 'success');
                    isItSuccess = true;

                }else{
                    formStatus.value = 'ERROR';
                    isItSuccess = false;
                   showToastify('Something went wrong', 'error')
                }
            }).catch(error => {
                if (error.response) {
                    if(error.response.status == 422) {
                        formStatus.value = 'ERROR';
                        let data = error.response.data;
                        if (data.errors) {
                            $.each(data.errors, function (index, item) {
                                showToastify(item[0], "error")
                            })
                        }
                    }
                }
                isItSuccess = false;
            });

            // catch(function(jqXhr, textStatus, errorThrown){
            //     let data = jqXhr.responseJSON;
            //     console.log(data)
            //     console.log(errorThrown)
            //     // let data = jqXhr.responseJSON;
            //     // if (data.errors) {
            //     //     $.each(data.errors, function (index, item) {
            //     //         showToastify(item[0], "error")
            //     //     })
            //     // }
            //     if (jqXhr.status == 500 || jqXhr.status == 400) {
            //         showToastify(response.message, "success")
            //     }
            // });



        } catch (e) {
            isItSuccess = false;
            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value = e.response.data.errors
                }
            }
        }
        return isItSuccess;
    }
    const removeLeadFromDeal = async (deal_id,lead_id) => {
        let data = {
            deal_id:deal_id,
            lead_id:lead_id
        };
        await axios.post(`/api/vue/v1/delete-lead`,data).then(function(response){
            if(response.data.status){
                getDeal(deal_id);
               // getDealLeads(deal_id)
                showToastify('Lead removed successfully', 'success');
            }else{
                showToastify('Something went wrong', 'error')
            }
        }).catch(function(){
            //console.log('FAILURE!!');
        });


    }
    const addNewLead = async (deal_id,lead_id,new_lead_type) => {
        let data = {
            deal_id:deal_id,
            lead_id:lead_id,
            lead_type: new_lead_type ? new_lead_type : 'Secondary'
        };
        await axios.post(`/api/vue/v1/add-new-lead`,data).then(function(response){
          console.log(response.data.status)

            if(response.data.status === '1'){
                getDeal(deal_id);
                showToastify(response.data.msg, 'success');
            }else{
                showToastify(response.data.msg, 'error')
            }
        }).catch(function(){
            //console.log('FAILURE!!');
        });


    }

    const getLeadSelect2 = async (search ='',agent_id = '',) => {
        let response = await axios.get(`/api/vue/v1/leads-select2/?search=${search}&agent_id=${agent_id}`)
        searchedLeads.value = response.data.data
    }
    const deleteMedia = async (id,model) => {
        let data = {
            id:id,
            model:model
        }
        let success = false;
        let response = await axios.post(`/api/vue/v1/delete-media`,data).then(function(response){
           console.log(response.data.status == '1')
            if(response.data.status == '1'){
                success = true;
                showToastify('File removed successfully', 'success');
            }else{
                success = false;
                showToastify('Unable to delete the file contact support', 'error')
            }
        }).catch(function(){
            success = false;
            //console.log('FAILURE!!');
        });
        return success;
    }
    const updateDeal = async (data) => {
        errors.value = ''
        try {
            const config = {
                headers: {
                    'Content-type': 'multipart/form-data'
                }
            }
            let response = await axios.post('/api/vue/v1/update-deal', data).then(function(response){

                if(response.data.status){

                    showToastify('Deal updated successfully', 'success');
                }else{
                    showToastify('Something went wrong', 'error')
                }
                return response;
            }).catch(function(){
                return response;
                //console.log('FAILURE!!');
            });

        return response;


        } catch (e) {
            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value = e.response.data.errors
                }
            }
            return e;
        }

    }
    const updateDealState = async (deal_id, state) => {
        let data = {
            deal_id:deal_id,
            state:state
        }
        errors.value = ''
        try {
            const config = {
                headers: {
                    'Content-type': 'multipart/form-data'
                }
            }
            let response = await axios.post('/api/vue/v1/update-deal-state', data).then(function(response){

                if(response.data.status){

                    //showToastify('Deal step updated successfully', 'success');
                }else{
                    showToastify('Something went wrong', 'error')
                }
                return response;
            }).catch(function(){
                return response;
                //console.log('FAILURE!!');
            });

            return response;


        } catch (e) {
            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value = e.response.data.errors
                }
            }
            return e;
        }

    }

    //deal splits
    const getDealCalculations = async (id) => {
        let response = await axios.get(route('get.deal.commission.calculations1')+'?deal_id='+id+`&refresh_calculations=true`)

        //check response http status
        if(response.status == 200){
            commissions.value = response.data.data
        }else{

            showToastify('Something went wrong', 'error')
        }
    }
    const getDealCommissions = async (id) => {

        let response = await axios.get(route('get.deal.getDealCommissions')+'?deal_id='+id)

        //check response http status
        if(response.status == 200){
            commissions.value = response.data.data
        }else{

            showToastify('Something went wrong', 'error')
        }
    }
    const sdfdf = async (data) => {
        try {
            const response = await axios.post('/api/vue/v1/add-deal-commissions', data);
            if (response.data.status) {
                const res = response.data.data;
                // Assuming getDeal and showToastify are defined elsewhere
                getDeal(res.id);
                showToastify('Deal commission added successfully', 'success');
            } else {
                showToastify('Something went wrong', 'error');
            }
            return response;
        } catch (error) {
            //handle 500 error

            //if (error.response.status === 500) {
                showToastify('Something went wrong', 'error');
           // }
            // Handle error
            console.error('Error adding data:', error);
            return error;
        }
    }
    const addDealCommissions = async (data,deal_id) => {

        errors.value = ''
        try {
            const config = {
                headers: {
                    'Content-type': 'multipart/form-data'
                }
            }
            let response = await axios.post('/api/vue/v1/add-deal-commissions', data).then(function(response){

                //get http response status
                let status = response.status;

                if(response.errors != null && response.errors != undefined && response.errors != ''){
                    errors.value = response.errors;
                    for (const key in response.errors) {
                        showToastify(response.errors[key], 'error')
                    }
                }
                if(response.status == 200) {

                    if (response.data.status == '1') {
                      //  getDeal(deal_id);
                        commissions.value = response.data.data
                        showToastify('Deal commission added successfully', 'success');

                    } else {
                        showToastify('Something went wrong', 'error')
                    }
                }else{
                    showToastify('Something went wrong', 'error')
                }
            }).catch(error => {
                if (error.response) {
                    if(error.response.status == 422) {
                        let data = error.response.data;
                        if (data.errors) {
                            $.each(data.errors, function (index, item) {
                                showToastify(item[0], "error")
                            })
                        }
                    }
                }

            });

        } catch (e) {

            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value = e.response.data.errors
                }
            }
        }

    }
    const createSplit = async (data) => {
        errors.value = ''
        try {
            const config = {
                headers: {
                    'Content-type': 'multipart/form-data'
                }
            }
            let response = await axios.post('/api/vue/v1/create-deal-split', data).then(function(response){

                if(response.data.status){
                    getDealSplits(data.deal_id,true);
                    showToastify('Split created successfully', 'success');
                }else{
                    showToastify('Something went wrong', 'error')
                }
                return response;
            }).catch(function(){
                return response;
                //console.log('FAILURE!!');
            });

            return response;


        } catch (e) {
            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value = e.response.data.errors
                }
            }
            return e;
        }

    }
    const getDealSplits = async (id,refresh = false) => {
       // let response = await axios.get(`/api/vue/v1/get-deal-split/?deal_id=${id}`)

        let response = await axios.get(route('get.deal.commission.calculations1')+'?deal_id='+id+`&refresh_calculations=`+refresh)

       //check response http status
        if(response.status == 200){
            splits.value = response.data.data

        }else{
            splits.value = []
            showToastify('Something went wrong', 'error')
        }
    }
    const deleteDealSplit = (id,deal_id) => {
        let data = {
            id:id,
            deal_id:deal_id
        }
        axios.post(`/api/vue/v1/deal-split-remove`,data).then((response) => {
            getDealSplits(deal_id,true);
        });
    }
    const getDealSplitsCommission = async (id) => {
        let response = await axios.get(`/api/vue/v1/get-deal-split-commission/?deal_id=${id}`)

       //check response http status
        if(response.status == 200){
            splits_commission.value = response.data.data
        }else{
            splits_commission.value = []
            showToastify('Something went wrong', 'error')
        }
    }
    const deleteDealCommission = (id,deal_id) => {
        axios.delete(`/api/vue/v1/delete-deal-commissions/${id}`).then((response) => {
            getDealCalculations(deal_id);
        })
    }
    const getDealDeductibleType = async (id) => {
        let response = await axios.get(`/api/vue/v1/get-deductible-type`)
        if(response.status == 200){
            deductibleType.value = response.data.data
        }else{
            deductibleType.value = []
            showToastify('Something went wrong', 'error')
        }
    }
    const getDealDeductibles = async (id) => {
        let response = await axios.get(`/api/vue/v1/get-deductible?deal_id=${id}`)
        if(response.status == 200){
            deductibles.value = response.data.data
        }else{
            deductibles.value = []
            showToastify('Unable to fetch deal deductible', 'error')
        }
    }
    const deleteDeductible = (id,deal_id) => {
        Swal.fire({
            html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to remove this lead from this deal ?</p></div></div>',


            showCancelButton: true,
            cancelButtonText: 'Cancel',
            cancelButtonClass: 'btn btn-warning action-close w-xs',

            showCloseButton: true,
            confirmButtonText: 'Delete',
            confirmButtonClass: 'btn btn-danger action-delete  w-xs me-2',

            buttonsStyling: false,

        }).then(function (result) {
            if (result.isConfirmed) {
                axios.delete(`/api/vue/v1/deductible-delete/${id}`).then((response) => {
                    getDealDeductibles(deal_id);
                })
            }
        })

    }
    const storeDealDeductible = async (data) => {
        errors.value = ''
        try {
            const config = {
                headers: {
                    'Content-type': 'multipart/form-data'
                }
            }
            let response = await axios.post('/api/vue/v1/store-deductible', data).then(function(response){

                if(response.data.status){
                    getDealDeductibles(data.deal_id);
                    showToastify('Fee added successfully', 'success');
                }else{
                    showToastify('Something went wrong', 'error')
                }
                return response;
            }).catch(function(){
                return response;
                //console.log('FAILURE!!');
            });

            return response;


        } catch (e) {
            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value = e.response.data.errors
                }
            }
            return e;
        }

    }
    const getProperty = async (id) => {
        let response = await axios.get(`/api/vue/v1/get-property/?id=${id}`)
        property.value = response.data.data
    }
    return {
        data,
        errors,
        formStatus,
        leads,
        notes,
        logs,
        lead,
        dealMedia,
        leadTypes,
        dealTasks,
        deal,
        searchedLeads,
        splits,
        splits_commission,
        commissions,
        deductibleType,
        deductibles,
        contact,
        property,
        getDealLeads,
        getNotes,
        addDealNote,
        getActivityLog,
        getLead,
        getDeal,
        getDealTypes,
        getLeadTypes,
        getDealTasks,
        addNewLead,
        updateLead,
        removeLeadFromDeal,
        getLeadSelect2,
        deleteMedia,
        updateDeal,
        updateDealState,
        updateDealDocuments,
        getDealMedia,
        deleteDealMedia,
        getDealSplits,
        createSplit,
        getDealSplitsCommission,
        deleteDealSplit,
        storeDealTask,
        getDealCommissions,
        addDealCommissions,
        getDealCalculations,
        storeDealDeductible,
        getDealDeductibleType,
        getDealDeductibles,
        deleteDeductible,
        deleteDealCommission,
        createDeal,
        updateContact,
        updateContactDocuments,
        addAgentInvitation,
        getContact,
        getProperty,
    }
}
