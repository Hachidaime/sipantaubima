<div class="content-header">
  <div class="container">
    <div class="row">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">{$title}</h1>
      </div>
      <!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          {section 'inner' $breadcrumb}
          <li class="breadcrumb-item">
            {if $breadcrumb[inner][1] ne ''}
            <a href="{$smarty.const.BASE_URL}/{$breadcrumb[inner][1]}">
              {$breadcrumb[inner][0]}
            </a>
            {else} {$breadcrumb[inner][0]} {/if}
          </li>
          {/section}
        </ol>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</div>
