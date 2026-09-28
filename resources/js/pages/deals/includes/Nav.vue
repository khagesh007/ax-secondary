<template>
   <div>
       <div class="row">
           <div class="col-xxl-12">
               <div class="card">
                   <div class="card-body">
                       <div class="step-arrow-nav">
                           <ul class="nav nav-pills nav-justified custom-nav" role="tablist">
                               <li class="nav-item" role="presentation">
                                   <button class="nav-link p-3 active" id="property-selection-tab" data-bs-toggle="pill" data-bs-target="#property-selection-step" type="button" role="tab" aria-controls="pills-bill-info" aria-selected="true">Property Details</button>
                               </li>
                               <li class="nav-item" role="presentation">
                                   <button class="nav-link p-3" id="property-details-tab" data-bs-toggle="pill" data-bs-target="#pills-bill-address" type="button" role="tab" aria-controls="pills-bill-address" aria-selected="false">Agent Details</button>
                               </li>
                               <li class="nav-item" role="presentation">
                                   <button class="nav-link p-3" id="pills-payment-tab" data-bs-toggle="pill" data-bs-target="#pills-payment" type="button" role="tab" aria-controls="pills-payment" aria-selected="false">Document Verification</button>
                               </li>
                               <li class="nav-item" role="presentation">
                                   <button class="nav-link p-3" id="pills-finish-tab" data-bs-toggle="pill" data-bs-target="#pills-finish" type="button" role="tab" aria-controls="pills-finish" aria-selected="false">Verified</button>
                               </li>
                           </ul>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>
</template>
