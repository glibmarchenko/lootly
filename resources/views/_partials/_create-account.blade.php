@if(!Auth::guest())
    <create-account <?php echo (isset($switchOnSuccess) && $switchOnSuccess) ? ':switch-on-success="1"' : ''; ?>></create-account>
@endif
