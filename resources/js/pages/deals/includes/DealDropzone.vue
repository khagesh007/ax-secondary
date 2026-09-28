<template>
    <div>
        <div ref="dropRef" class="dropzone custom-dropzone"></div>
        <div class="dropzone preview-container d-none"></div>
    </div>
</template>

<script setup>

import {ref, onMounted, defineComponent} from 'vue'
import {Dropzone} from 'dropzone'
const props = defineProps({
        deal_id : {
          type : String,
          required: true
        },
        paramName: {
            type: String,
            required: true
        }

})
//getting the div container
const dropRef = ref(null)
//creating html custom preview for uploading files
const customPreview = `
        <div class="d-flex flex-wrap dz-preview dz-processing dz-image-preview dz-complete">
          <div class="dz-image">
            <img data-dz-thumbnail>
          </div>
          <div class="dz-details">
            <div class="dz-size"><span data-dz-size></span></div>
              <div class="dz-filename"><span data-dz-name></span></div>
            </div>
            <div class="dz-progress">
              <span class="dz-upload" data-dz-uploadprogress></span>
            </div>
            <div class="dz-error-message"><span data-dz-errormessage></span></div>
            <div class="dz-success-mark">
                <i class="bi bi-check-circle-fill" style="font-size: 2rem; color: green;"></i>
            </div>
            <div class="dz-error-mark">
                <i class="bi bi-exclamation-circle-fill" style="font-size: 2rem; color: red;"></i>
          </div>
        </div>
      `;
onMounted(() => {
    // Configuring Dropzone and Adding to div element
    if (dropRef.value !== null) {
        new Dropzone(dropRef.value, {
            url: '/api/vue/v1/add-deal-doc',//backend url
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content
            },
            paramName: "file", // input name,
            init: function () {
                this.on("sending", function (file, xhr, formData) {
                    formData.append("deal_id", props.deal_id);
                    formData.append("title", "test");
                });
            },
            maxFilesize: 10, // MB
           // maxFiles: 10,
            addRemoveLinks: true,
            autoProcessQueue: true,

            acceptedFiles: ".jpg,.png,.jpeg,.webp,.pdf,.doc,.docx,.zip" ,// accepted files
            previewTemplate: customPreview,
            previewsContainer: dropRef.value.parentElement.querySelector('.preview-container'),
            success: function (file, response) {
                console.log(response)
                if (response.status == 'success') {

                }
            },

        })
        // customizing the input field of dropzone
        if (dropRef.value.querySelector('.dz-default')) {
            dropRef.value.querySelector('.dz-default').innerHTML = `
              <div style="display: flex; justify-content: center;">
              <i class="ri ri-upload-cloud-line" style="font-size: 5rem;color: #adadad;"></i>

              </div>
              <p style="text-align: center; margin: 0;color: #6f6f6f;"><strong>Drag and drop files to upload</strong></p>
            `
        }
    }
})
</script>

<style scoped>
.custom-dropzone {
    border-style: dashed;
    border-width: 1px;
    border-color: rgb(221, 221, 221);
    padding: 20px;
    color: rgb(114, 114, 114)
}

</style>
