<template>
  <div class="modal zoomIn" id="viewFileModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content">
        <div class="modal-header bg-soft-danger">
          <h5 class="modal-title" id="viewFileModalLabel">{{ file?.title }}</h5>

          <button type="button" class="btn-close" @click="modalClose()"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <iframe id="pdfIFrame" :src="file?.path" frameborder="0" class="responsive-iframe"
                      width="100%" height="700px" v-if="file?.extension == 'pdf'"></iframe>
              <img :src="file?.path" :alt="file?.title"
                   v-if="isImageFile">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a :href="file?.path" target="_blank" class="btn btn-success">
            Download
          </a>
          <button type="button" class="btn btn-light action-close" @click="modalClose()">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const emit = defineEmits(["toggleViewFileModal"]);

const props = defineProps({
  file: {
    type: Object || null,
    required: true
  }
});

const formLoading = ref(false);

const modalClose = async () => {
  emit("toggleViewFileModal", null, false);
}

const isImageFile = () => {
  return ["png", "jpg", "jpeg", "gif"].includes(file.extension);
}

const modal = ref(null);

onMounted(() => {
  const modalEl = document.getElementById("viewFileModal");
  document.body.appendChild(modalEl);

  modal.value = new bootstrap.Modal(modalEl, {
    keyboard: false,
  });

  modal.value.show();
})

onUnmounted(() => {
  modal.value.hide()
})

</script>
<style scoped></style>
