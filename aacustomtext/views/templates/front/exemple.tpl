<div id="custom-text">
  <div class="card-body">
    <div class="col-sm-12 col-lg-4"></div>
    <div class="col-sm-12 col-lg-4">
      <h1 class="title-custom-text">ACDIS France</h1>
        {foreach from=$column.firstColumn key=id item=section}
          <h2 class="second-title-custom">
              {$text = 'CUSTOM_TEXT_TITRE_' }
              {$text = $text|cat:$id}
              {$section.$text}
          </h2>
          <p class="p-custome-text">
              {$description = 'CUSTOM_TEXT_DESCRIPTION_' }
              {$description = $description|cat:$id}
              {$section.$description}

          </p>
        {/foreach}
    </div>
    <div class="col-sm-12 col-lg-4">
        {foreach from=$column.secondColumn key=id item=section}
            {$id = $id + $column.firstColumn|@count}
          <h2 class="second-title-custom">
              {$titre = 'CUSTOM_TEXT_TITRE_' }
              {$titre = $titre|cat:$id}
              {$section.$titre}
          </h2>
          <p class="p-custome-text">
              {$description = 'CUSTOM_TEXT_DESCRIPTION_' }
              {$description = $description|cat:$id}
              {$section.$description}
          </p>
        {/foreach}
    </div>

  </div>
</div>
