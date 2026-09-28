<style>
    *.hidden {
        display: none !important;
    }

    div.loading {
        position: fixed!important;
        z-index: 9999;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(16, 16, 16, 0.5);
    }

.loading2{
    position: fixed!important;
        z-index: 9999;
        top: 50%;
        left: 50%;

}
</style>



<div class="loading d-none" id="screen_loader">
    <div class='loading2'>
        <div class="spinner-border text-primary" role="status">
        </div>
    </div>
</div>
