<?php echo $header; ?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="/server/js/vendor/jquery.ui.widget.js"></script>
<script src="/server/js/jquery.iframe-transport.js"></script>
<script src="/server/js/jquery.fileupload.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link rel="stylesheet" href="/server/css/jquery.fileupload.css">

<script>
  $(function () {
    'use strict';
    // Change this to the location of your server-side upload handler:
    var url = '/server/php/';

    $('#fileupload').fileupload({
        url: url,
        dataType: 'json',
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                $('<p/>').text(file.name).appendTo('#files');
            });

            $.each(data.result.files, function (index, file) {
                $('<input type="hidden" class="files" value="'+file.name+'" />').appendTo('#files');
            });


        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        }
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
});
</script>
</script>
<?php echo $column_left; ?>

<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-store" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <section class="row">
          <section class="col-lg-6">
            <!-- Magic happens here -->
            <span class="btn btn-success fileinput-button">
                <i class="glyphicon glyphicon-plus"></i>
                <span>Selecione os arquivos</span>
                <input id="fileupload" type="file" name="files[]" multiple>
            </span>
            <br>
            <br>
            <div id="progress" class="progress">
                <div class="progress-bar progress-bar-success"></div>
            </div>
            <div id="files" class="files"></div>
          </section>
          <section class="col-lg-6">
            <form action="">
               <div class="form-group">
                <label>Categoria</label>
                 <select id="category" class="form-control">
                  <option value="0">Selecione</option>
                  <?php foreach ($categories as $category) {
                    echo "<option value=\"$category[category_id]\">$category[name]</option>";
                  } ?>
                </select>
              </div>
              <div class="form-group">
                <label>Preço 10x15</label>
                <input type="email" class="form-control" id="price1" placeholder="Preço">
              </div>
              <div class="form-group">
                <label>Preço 13x18</label>
                <input type="email" class="form-control" id="price2" placeholder="Preço">
              </div>
              <div class="form-group">
                <label>Preço 15x21</label>
                <input type="email" class="form-control" id="price3" placeholder="Preço">
              </div>
              <button type="button" class="btn btn-success" id="cadastrar">Cadastrar produtos</button>
            </form>
          </section>
        </section>

      </div>
    </div>
  </div>
</div>
<div id="modal">Aguarde...</div>
<script>
  $('#modal').hide();
  $('#cadastrar').click(function(){
    errors = 0;
    if($('#category').val() == 0) {
      alert('Selecione uma categoria');
      errors++;
    }

    if((errors == 0) && ($('#price1').val() == '')) {
      alert('Informe o preço para fotos 10x15');
      errors++;
    }
    if((errors == 0) && ($('#price2').val() == '')) {
     // alert('Informe o preço para fotos 13x18');
      errors++;
    }
    if((errors == 0) && ($('#price3').val() == '')) {
      alert('Informe o preço para fotos 15x21');
      errors++;
    }
    if(errors == 0) {
      //realiza o cadastramento
      $('#modal').show();

      //itera imagens
      $('input[class="files"]' ).each(function( index ) {
         //cadastra cada uma das imagens como produto
         $.ajax({
            url: '/admin/index.php?route=module/mass/put&token='+'<?php echo $_GET['token']; ?>',
            type: 'POST',
            async: false,
            datatype: 'json',
            data: { 
              file: $(this).val(),
              category_id : $('#category').val(),
              price1 : $('#price1').val(),
              price2 : $('#price2').val(),
              price3 : $('#price3').val(), 
            },
            success: function(json) {
              if(json != 'sucesso') {
                alert('Erro de Gravação! '+json);
                $('#modal').hide();
              }
            }
          });
      });
      alert('Produtos cadastrados com sucesso!');
      window.location = '/admin/index.php?route=catalog/product&token='+'<?php echo $_GET['token']; ?>';
    }
  })
</script>

<?php echo $footer; ?>