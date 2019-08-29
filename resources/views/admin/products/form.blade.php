<div id="vue-product-form" class="box box-info" v-cloak>
  <div class="box-header with-border">
    <h3 class="box-title">{{ !$product->id ? '添加' : '编辑' }}</h3>
    <div class="box-tools">
      @if ($product->id)
      <div class="btn-group pull-right" style="margin-right: 5px">
        <a href="javascript:void(0);" class="btn btn-sm btn-danger btn-delete" title="刪除">
          <i class="fa fa-trash"></i> <span class="hidden-xs">刪除</span>
        </a>
      </div>
      @endif
      <div class="btn-group pull-right" style="margin-right: 5px">
        <a href="{{ admin_url('products') }}" class="btn btn-sm btn-default" title="列表"><i class="fa fa-list"></i> <span class="hidden-xs">列表</span></a>
      </div>
    </div>
  </div>
  <!-- /.box-header -->

  <!-- form start -->
  <form action="{{ admin_url('products' . ($product->id ? '/' . $product->id : '')) }}" method="post" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">
    @csrf
    @if ($product->id)
      @method('PUT')
    @endif

    <div class="box-body">
      <div class="fields-group">
        <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
          <label for="title" class="col-sm-2 control-label">商品名称</label>
          <div class="col-sm-8">
            @include('admin.fields.errors', ['key' => 'title'])
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
              <input type="text" id="title" name="title" value="{{ old('title', $product->title) }}" class="form-control title" placeholder="输入 商品名称" />
            </div>
          </div>
        </div>

        <div class="form-group {{ $errors->has('thumb') ? 'has-error' : '' }}">
          <label for="image" class="col-sm-2 control-label">封面图片</label>
          <div class="col-sm-8">
            @include('admin.fields.errors', ['key' => 'thumb'])
            <input type="file" class="thumb" name="image" @if($product->thumb) data-initial-preview="{{ $product->thumb }}" data-initial-caption="{{ basename($product->thumb) }}" @endif />
          </div>
        </div>

        <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
          <label for="description" class="col-sm-2 control-label">商品介紹</label>
          <div class="col-sm-8">
            @include('admin.fields.errors', ['key' => 'description'])
            <textarea class="form-control description" id="description" name="description" placeholder="输入 商品介紹">{{ old('description', $product->description) }}</textarea>
          </div>
        </div>

        <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
          <label for="on_sale" class="col-sm-2 control-label">是否上架</label>
          <div class="col-sm-8">
            @include('admin.fields.errors', ['key' => 'status'])
            <div>
              <input type="checkbox" class="on_sale la_checkbox" @if(!is_null(old('status')) ? old('status') === 'on' : $product->status) checked @endif />
              <input type="hidden" name="on_sale" value="{{ old('status') ?? ($product->status ? 'on' : 'off') }}" />
            </div>
          </div>
        </div>

        <div class="form-group {{ $errors->has('price') ? 'has-error' : '' }}">
          <label for="price" class="col-sm-2 control-label">价格</label>
          <div class="col-sm-8">
            @include('admin.fields.errors', ['key' => 'price'])
            <div v-show="!showMinPrice" class="input-group">
              <input style="width: 100px" type="text" id="price" name="price" value="{{ old('price', $product->price) ?? 0 }}" class="form-control price" placeholder="输入 价格" min="0" />
            </div>
          </div>
        </div>

      </div>
    </div>
    <!-- /.box-body -->

    <div class="box-footer">
      <div class="col-md-2"></div>
      <div class="col-md-8">
        <div class="btn-group pull-right">
          <button type="submit" class="btn btn-primary">确认</button>
        </div>
        <label class="pull-right" style="margin: 5px 10px 0 0;">
          <input type="checkbox" class="after-submit" name="after-save" value="1"> 继续编辑
        </label>
        <label class="pull-right" style="margin: 5px 10px 0 0;">
          <input type="checkbox" class="after-submit" name="after-save" value="2"> 查看
        </label>
        <div class="btn-group pull-left">
          <button type="reset" class="btn btn-warning">重置</button>
        </div>
      </div>
    </div>
    <!-- /.box-footer -->
  </form>
</div>

<script>
  function init() {
    const token = '{{ csrf_token() }}';

    $('.btn-delete').unbind('click').click(function () {
      swal({
        title: '确认刪除？',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: '确认',
        showLoaderOnConfirm: true,
        cancelButtonText: '取消',
        preConfirm: function () {
          return new Promise(function (resolve) {
            $.ajax({
              method: 'post',
              url: '{{ admin_url('products/' . $product->id) }}',
              data: {
                _method: 'delete',
                _token: token,
              },
              success: function (data) {
                $.pjax({
                  container: '#pjax-container',
                  url: '{{ admin_url('products') }}'
                });
                resolve(data);
              }
            });
          });
        }
      }).then(function (result) {
        const data = result.value;
        if (typeof data === 'object') {
          swal(data.message, '', data.status ? 'success' : 'error');
        }
      });
    });

    $('input.image').fileinput({
      overwriteInitial: true,
      initialPreviewAsData: true,
      browseLabel: '浏览',
      showRemove: false,
      showUpload: false,
      deleteExtraData: {
        image: '_file_del_',
        _file_del_: '',
        _token: token,
        _method: 'DELETE'
      },
      deleteUrl: '{{ admin_url('products/' . $product->id) }}',
      allowedFileTypes: ['image']
    });

    if (!document.getElementById('cke_description')) {
      CKEDITOR.replace('description');
    }

    $('.on_sale.la_checkbox').bootstrapSwitch({
      size:'small',
      onText: '是',
      offText: '否',
      onColor: 'primary',
      offColor: 'default',
      onSwitchChange: function (event, state) {
        $(event.target).closest('.bootstrap-switch').next().val(state ? 'on' : 'off').change();
      }
    });

    $('.price:not(.initialized)')
      .addClass('initialized')
      .bootstrapNumber({
        upClass: 'success',
        downClass: 'primary',
        center: true
      });

    $('.after-submit').iCheck({ checkboxClass: 'icheckbox_minimal-blue' })
      .on('ifChecked', function () {
        $('.after-submit').not(this).iCheck('uncheck');
      });
  }
</script>
