{extends file="Templates/mainlayout.php"}

{block name="content"}
<div class="alert alert-{$data.color} text-center mb-0" role="alert">
  <p>{$data.icon} {$data.message}</p>
  <a href="{$smarty.const.BASE_URL}/{$data.module|lower}" class="alert-link">Back to {$data.module}</a>
</div>
{/block}