{if isset($video) && $video}
<!-- Video -->
<section class="page-product-box">
    <h3 class="page-product-heading">{l s='Video' mod='myprestamodule1'}</h3>
    <!-- The Youtube Player -->
    <div class="myprestamodule1_video-container">
        <iframe src="https://www.youtube.com/embed/{$video->key}">
    </div>
    </iframe>
</section>
<!--end Video -->
{/if}