<div class="row section-border-bottom p-t-25 p-b-25">
	<div class="col-md-12">
		<div class="rewards-page-editor">
		    <trumbowyg v-model="htmlMode.body" :config="htmlMode.config" name="htmlMode.body"></trumbowyg>
		</div>
	</div>
</div>

<div class="row m-t-20 p-b-10">
    <div class="col-md-12">
        <button type="button" 
        		class="btn btn-default p-r-15 p-l-15 pull-right" 
        		:disabled="true"
        		@click="resetCustomizations" style="display:none;" >
            Reset Customizations
        </button>
    </div>
</div>
