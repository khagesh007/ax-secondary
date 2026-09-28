<template>
     <LiteYouTubeEmbed :id="id"
     title="YouTube video player"
     ref="iframe"
     wrapperClass="youtube-embed-wrapper"

     />
</template>
<script setup>
import {ref,onMounted} from "vue";
import LiteYouTubeEmbed from 'vue-lite-youtube-embed'
import 'vue-lite-youtube-embed/style.css'
const iframe = ref(null)
const props = defineProps(
    {
        youtube_url: {
            type: String,
            required: true
        }
    }
);
const id = ref('');
onMounted(() => {
    //get the youtube video id from the url
    const url = new URL(props.youtube_url);
    const video_id = url.searchParams.get('v');
    //set the video id to the component
    id.value = video_id;

})
</script>

<style scoped>
.youtube-embed-wrapper{
    position: relative;
    padding-bottom: 56.25%;
    height: 20px;
    overflow: hidden;
    max-width: 100%;
    margin: 0 auto;
}
</style>
