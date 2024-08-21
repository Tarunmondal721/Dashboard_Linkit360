<footer class="main-footer bottom-0 px-4 py-4" id="footer_main">
    <div class="d-flex align-items-center my-2 pull-right">
        <span class="badge badge-secondary px-2 bg-primary" id="loadTimer">Load Time :{{ round(microtime(true) - LARAVEL_START, 3) }}s</span>
    </div>
    <div class="footer-left">
         {{ (Utility::getValByName('footer_text')) ? Utility::getValByName('footer_text') :config('app.name', 'Federal Ministry Of Health') }} {{date('Y')}}
    </div>
    <div class="footer-right">
    </div>
</footer>
