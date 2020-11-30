<!-- prettier-ignore -->
{extends file='Templates/mainlayout.tpl'}

{block 'content'}
<div class="triangle">
  <div class="container d-flex flex-column flex-md-row">
    <div class="flex-fill px-2 py-5">
      <div class="text-center">
        <a href="{$smarty.const.BASE_URL}/login">
          <img
            src="https://dummyimage.com/200x200/ff0000/fff.png&text=1:1+(200x200)"
            class="rounded-circle"
            alt="..."
          />
        </a>
      </div>
      <div class="text-center py-3">Pembangunan<br />Jalan & Jembatan</div>
    </div>
    <div class="flex-fill px-2 py-5">
      <div class="text-center">
        <a href="{$smarty.const.BASE_URL}/login">
          <img
            src="https://dummyimage.com/200x200/00ff00/fff.png&text=1:1+(200x200)"
            class="rounded-circle"
            alt="..."
          />
        </a>
      </div>
      <div class="text-center py-3">Pemeliharaan<br />Talut & Jembatan</div>
    </div>
    <div class="flex-fill px-2 py-5">
      <div class="text-center">
        <a href="{$smarty.const.BASE_URL}/login">
          <img
            src="https://dummyimage.com/200x200/0000ff/fff.png&text=1:1+(200x200)"
            class="rounded-circle"
            alt="..."
          />
        </a>
      </div>
      <div class="text-center py-3">Penerangan<br />Jalan Umum</div>
    </div>
  </div>
</div>

<div
  id="carouselExampleIndicators"
  class="carousel slide mt-5 container"
  data-ride="carousel"
>
  <ol class="carousel-indicators">
    <li
      data-target="#carouselExampleIndicators"
      data-slide-to="0"
      class="active"
    ></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
  </ol>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img
        src="https://dummyimage.com/960x300/ccc/666.png&text=16:5+(960x300)"
        class="d-block w-100"
        alt="..."
      />
    </div>
    <div class="carousel-item">
      <img
        src="https://dummyimage.com/960x300/aaa/666.png&text=16:5+(960x300)"
        class="d-block w-100"
        alt="..."
      />
    </div>
    <div class="carousel-item">
      <img
        src="https://dummyimage.com/960x300/999/666.png&text=16:5+(960x300)"
        class="d-block w-100"
        alt="..."
      />
    </div>
  </div>
  <a
    class="carousel-control-prev"
    href="#carouselExampleIndicators"
    role="button"
    data-slide="prev"
  >
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a
    class="carousel-control-next"
    href="#carouselExampleIndicators"
    role="button"
    data-slide="next"
  >
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
{/block}
