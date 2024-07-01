<template>
    <div id="editor-content" class="ck ck-reset ck-editor ck-rounded-corners">
        <div class="ck ck-editor__main">
            <div 
                class="ck ck-content ck-editor__editable ck-rounded-corners ck-editor__editable_inline ck-blurred inline-block w-full break-all whitespace-break-spaces"
                v-html="content"
            />
        </div>
    </div>
</template>
<script >
export default {
    name: "ContentCkeditorComponent",
    props: {
        content: { type: String, required: false }
    },
};
</script>
<style>
#editor-content .ck.ck-editor__main > .ck-editor__editable:not(.ck-focused) {
    border: none !important;
}
#editor-content .ck.ck-editor__editable_inline {
    padding: 0;
}
#editor-content .ck.ck-editor__editable_inline > *:first-child {
    margin-top: 0;
}
</style>