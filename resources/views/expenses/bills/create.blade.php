@extends('layouts.admin')

@section('title', trans('general.title.new', ['type' => trans_choice('general.bills', 1)]))

@section('content')
<!-- Default box -->
<div class="box box-success">
    {!! Form::open(['url' => 'expenses/bills', 'files' => true, 'role' => 'form']) !!}

    <div class="box-body">
        <div class="form-group col-md-6 required {{ $errors->has('vendor_id') ? 'has-error' : ''}}">
            {!! Form::label('vendor_id', trans_choice('general.vendors', 1), ['class' => 'control-label']) !!}
            <div class="input-group">
                <div class="input-group-addon"><i class="fa fa-user"></i></div>
                {!! Form::select('vendor_id', $vendors, null, array_merge(['id' => 'vendor_id', 'class' => 'form-control', 'placeholder' => trans('general.form.select.field', ['field' => trans_choice('general.vendors', 1)])])) !!}
                <span class="input-group-btn">
                    <button type="button" onclick="createVendor();" class="btn btn-primary">{{ trans('general.add_new') }}</button>
                </span>
            </div>
            {!! $errors->first('vendor_id', '<p class="help-block">:message</p>') !!}
        </div>

        {{ Form::selectGroup('currency_code', trans_choice('general.currencies', 1), 'exchange', $currencies, setting('general.default_currency')) }}

        {{ Form::textGroup('billed_at', trans('bills.bill_date'), 'calendar',['id' => 'billed_at', 'class' => 'form-control', 'required' => 'required', 'data-inputmask' => '\'alias\': \'yyyy/mm/dd\'', 'data-mask' => ''],Date::now()->toDateString()) }}

        {{ Form::textGroup('due_at', trans('bills.due_date'), 'calendar',['id' => 'due_at', 'class' => 'form-control', 'required' => 'required', 'data-inputmask' => '\'alias\': \'yyyy/mm/dd\'', 'data-mask' => ''],Date::now()->toDateString()) }}

        {{ Form::textGroup('bill_number', trans('bills.bill_number'), 'file-text-o') }}

        {{ Form::textGroup('order_number', trans('bills.order_number'), 'shopping-cart',[]) }}

        <div class="form-group col-md-12">
            {!! Form::label('items', trans_choice('general.items', 1), ['class' => 'control-label']) !!}
            <div class="table-responsive">
                <table class="table table-bordered" id="items">
                    <thead>
                        <tr style="background-color: #f9f9f9;">
                            <th width="5%"  class="text-center">{{ trans('general.actions') }}</th>
                            <th width="40%" class="text-left">{{ trans('general.name') }}</th>
                            <th width="5%" class="text-center">{{ trans('bills.quantity') }}</th>
                            <th width="10%" class="text-right">{{ trans('bills.price') }}</th>
                            <th width="15%" class="text-right">{{ trans_choice('general.taxes', 1) }}</th>
                            <th width="10%" class="text-right">{{ trans('bills.total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $item_row = 0; ?>
                        <tr id="item-row-{{ $item_row }}">
                            <td class="text-center" style="vertical-align: middle;">
                                <button type="button" onclick="$(this).tooltip('destroy'); $('#item-row-{{ $item_row }}').remove(); totalItem();" data-toggle="tooltip" title="{{ trans('general.delete') }}" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>
                            </td>
                            <td>
                                <input class="form-control typeahead" required="required" placeholder="{{ trans('general.form.enter', ['field' => trans_choice('bills.item_name', 1)]) }}" name="item[{{ $item_row }}][name]" type="text" id="item-name-{{ $item_row }}">
                                <input name="item[{{ $item_row }}][item_id]" type="hidden" id="item-id-{{ $item_row }}">
                            </td>
                            <td>
                                <input class="form-control text-center" required="required" name="item[{{ $item_row }}][quantity]" type="text" id="item-quantity-{{ $item_row }}">
                            </td>
                            <td>
                                <input class="form-control text-right" required="required" name="item[{{ $item_row }}][price]" type="text" id="item-price-{{ $item_row }}">
                            </td>
                            <td>
                                {!! Form::select('item[' . $item_row . '][tax_id]', $taxes, setting('general.default_tax'), ['id'=> 'item-tax-'. $item_row, 'class' => 'form-control select2', 'placeholder' => trans('general.form.select.field', ['field' => trans_choice('general.taxes', 1)])]) !!}
                            </td>
                            <td class="text-right" style="vertical-align: middle;">
                                <span id="item-total-{{ $item_row }}">0</span>
                            </td>
                        </tr>
                        <?php $item_row++; ?>
                        <tr id="addItem">
                            <td class="text-center"><button type="button" onclick="addItem();" data-toggle="tooltip" title="{{ trans('general.add') }}" class="btn btn-xs btn-primary" data-original-title="{{ trans('general.add') }}"><i class="fa fa-plus"></i></button></td>
                            <td class="text-right" colspan="5"></td>
                        </tr>
                        <tr>
                            <td class="text-right" colspan="5"><strong>{{ trans('bills.sub_total') }}</strong></td>
                            <td class="text-right"><span id="sub-total">0</span></td>
                        </tr>
                        <tr>
                            <td class="text-right" colspan="5"><strong>{{ trans_choice('general.taxes', 1) }}</strong></td>
                            <td class="text-right"><span id="tax-total">0</span></td>
                        </tr>
                        <tr>
                            <td class="text-right" colspan="5"><strong>{{ trans('bills.total') }}</strong></td>
                            <td class="text-right"><span id="grand-total">0</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        {{ Form::textareaGroup('notes', trans_choice('general.notes', 2)) }}

        {{ Form::fileGroup('attachment', trans('general.attachment'),[]) }}
    </div>
    <!-- /.box-body -->

    <div class="box-footer">
        {{ Form::saveButtons('expenses/bills') }}
    </div>
    <!-- /.box-footer -->

    {!! Form::close() !!}
@endsection

@push('js')
    <script src="{{ asset('vendor/almasaeed2010/adminlte/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('public/js/bootstrap-fancyfile.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
@endpush

@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/almasaeed2010/adminlte/plugins/datepicker/datepicker3.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/bootstrap-fancyfile.css') }}">
@endpush

@push('scripts')
    <script type="text/javascript">
        var item_row = {{ $item_row }};

        function addItem() {
            html  = '<tr id="item-row-' + item_row + '">';
            html += '  <td class="text-center" style="vertical-align: middle;">';
            html += '      <button type="button" onclick="$(this).tooltip(\'destroy\'); $(\'#item-row-' + item_row + '\').remove(); totalItem();" data-toggle="tooltip" title="{{ trans('general.delete') }}" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>';
            html += '  </td>';
            html += '  <td>';
            html += '      <input class="form-control typeahead" required="required" placeholder="{{ trans('general.form.enter', ['field' => trans_choice('bills.item_name', 1)]) }}" name="item[' + item_row + '][name]" type="text" id="item-name-' + item_row + '">';
            html += '      <input name="item[' + item_row + '][item_id]" type="hidden" id="item-id-' + item_row + '">';
            html += '  </td>';
            html += '  <td>';
            html += '      <input class="form-control text-center" required="required" name="item[' + item_row + '][quantity]" type="text" id="item-quantity-' + item_row + '">';
            html += '  </td>';
            html += '  <td>';
            html += '      <input class="form-control text-right" required="required" name="item[' + item_row + '][price]" type="text" id="item-price-' + item_row + '">';
            html += '  </td>';
            html += '  <td>';
            html += '      <select class="form-control select2" name="item[' + item_row + '][tax_id]" id="item-tax-' + item_row + '">';
            html += '         <option selected="selected" value="">{{ trans('general.form.select.field', ['field' => trans_choice('general.taxes', 1)]) }}</option>';
            @foreach($taxes as $tax_key => $tax_value)
            html += '         <option value="{{ $tax_key }}">{{ $tax_value }}</option>';
            @endforeach
            html += '      </select>';
            html += '  </td>';
            html += '  <td class="text-right" style="vertical-align: middle;">';
            html += '      <span id="item-total-' + item_row + '">0</span>';
            html += '  </td>';

            $('#items tbody #addItem').before(html);
            //$('[rel=tooltip]').tooltip();

            $('[data-toggle="tooltip"]').tooltip('hide');

            $('#item-row-' + item_row + ' .select2').select2({
                placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.taxes', 1)]) }}"
            });

            item_row++;
        }

        $(document).ready(function(){
            //Date picker
            $('#billed_at').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });

            //Date picker
            $('#due_at').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });

            $(".select2").select2({
                placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.taxes', 1)]) }}"
            });

            $("#vendor_id").select2({
                placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.vendors', 1)]) }}"
            });

            $("#currency_code").select2({
                placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.currencies', 1)]) }}"
            });

            $('#attachment').fancyfile({
                text  : '{{ trans('general.form.select.file') }}',
                style : 'btn-default',
                placeholder : '{{ trans('general.form.no_file_selected') }}'
            });

            var autocomplete_path = "{{ url('items/items/autocomplete') }}";

            $(document).on('click', '.form-control.typeahead', function() {
                input_id = $(this).attr('id').split('-');

                item_id = parseInt(input_id[input_id.length-1]);

                $(this).typeahead({
                    minLength: 3,
                    displayText:function (data) {
                        return data.name;
                    },
                    source: function (query, process) {
                        $.ajax({
                            url: autocomplete_path,
                            type: 'GET',
                            dataType: 'JSON',
                            data: 'query=' + query + '&type=bill&currency_code=' + $('#currency_code').val(),
                            success: function(data) {
                                return process(data);
                            }
                        });
                    },
                    afterSelect: function (data) {
                        $('#item-id-' + item_id).val(data.item_id);
                        $('#item-quantity-' + item_id).val('1');
                        $('#item-price-' + item_id).val(data.purchase_price);
                        $('#item-tax-' + item_id).val(data.tax_id);

                        // This event Select2 Stylesheet
                        $('#item-tax-' + item_id).trigger('change');

                        $('#item-total-' + item_id).html(data.total);

                        totalItem();
                    }
                });
            });

            $(document).on('change', '#currency_code, #items tbody select', function(){
                totalItem();
            });

            $(document).on('keyup', '#items tbody .form-control', function(){
                totalItem();
            });

            $(document).on('change', '#vendor_id', function (e) {
                $.ajax({
                    url: '{{ url("expenses/vendors/currency") }}',
                    type: 'GET',
                    dataType: 'JSON',
                    data: 'vendor_id=' + $(this).val(),
                    success: function(data) {
                        $('#currency_code').val(data.currency_code);

                        // This event Select2 Stylesheet
                        $('#currency_code').trigger('change');
                    }
                });
            });
        });

        function totalItem() {
            $.ajax({
                url: '{{ url("items/items/totalItem") }}',
                type: 'POST',
                dataType: 'JSON',
                data: $('#currency_code, #items input[type=\'text\'],#items input[type=\'hidden\'], #items textarea, #items select'),
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(data) {
                    if (data) {
                        $.each( data.items, function( key, value ) {
                            $('#item-total-' + key).html(value);
                        });

                        $('#sub-total').html(data.sub_total);
                        $('#tax-total').html(data.tax_total);
                        $('#grand-total').html(data.grand_total);
                    }
                }
            });
        }

        function createVendor() {
            $('#modal-create-vendor').remove();

            modal  = '<div class="modal fade" id="modal-create-vendor" style="display: none;">';
            modal += '  <div class="modal-dialog  modal-lg">';
            modal += '      <div class="modal-content">';
            modal += '          <div class="modal-header">';
            modal += '              <h4 class="modal-title">{{ trans('general.title.new', ['type' => trans_choice('general.vendors', 1)]) }}</h4>';
            modal += '          </div>';
            modal += '          <div class="modal-body">';
            modal += '              {!! Form::open(['id' => 'form-create-vendor', 'role' => 'form']) !!}';
            modal += '              <div class="row">';
            modal += '                  <div class="form-group col-md-6 required">';
            modal += '                      <label for="name" class="control-label">{{ trans('general.name') }}</label>';
            modal += '                      <div class="input-group">';
            modal += '                          <div class="input-group-addon"><i class="fa fa-id-card-o"></i></div>';
            modal += '                          <input class="form-control" placeholder="{{ trans('general.name') }}" required="required" name="name" type="text" id="name">';
            modal += '                      </div>';
            modal += '                  </div>';
            modal += '                  <div class="form-group col-md-6">';
            modal += '                      <label for="email" class="control-label">{{ trans('general.email') }}</label>';
            modal += '                      <div class="input-group">';
            modal += '                          <div class="input-group-addon"><i class="fa fa-envelope"></i></div>';
            modal += '                          <input class="form-control" placeholder="{{ trans('general.email') }}" required="required" name="email" type="text" id="email">';
            modal += '                      </div>';
            modal += '                  </div>';
            modal += '                  <div class="form-group col-md-6">';
            modal += '                      <label for="tax_number" class="control-label">{{ trans('general.tax_number') }}</label>';
            modal += '                      <div class="input-group">';
            modal += '                          <div class="input-group-addon"><i class="fa fa-percent"></i></div>';
            modal += '                          <input class="form-control" placeholder="{{ trans('general.tax_number') }}" name="tax_number" type="text" id="tax_number">';
            modal += '                      </div>';
            modal += '                  </div>';
            modal += '                  <div class="form-group col-md-6 required">';
            modal += '                      <label for="email" class="control-label">{{ trans_choice('general.currencies', 1) }}</label>';
            modal += '                      <div class="input-group">';
            modal += '                          <div class="input-group-addon"><i class="fa fa-exchange"></i></div>';
            modal += '                          <select class="form-control" required="required" id="currency_code" name="currency_code">';
            modal += '                              <option value="">{{ trans('general.form.select.field', ['field' => trans_choice('general.currencies', 1)]) }}</option>';
            @foreach($currencies as $currency_code => $currency_name)
            modal += '                              <option value="{{ $currency_code }}" {{ (setting('general.default_currency') == $currency_code) ? 'selected' : '' }}>{{ $currency_name }}</option>';
            @endforeach
            modal += '                          </select>';
            modal += '                      </div>';
            modal += '                  </div>';
            modal += '                  <div class="form-group col-md-12">';
            modal += '                      <label for="address" class="control-label">{{ trans('general.address') }}</label>';
            modal += '                      <textarea class="form-control" placeholder="{{ trans('general.address') }}" rows="3" name="address" cols="50" id="address"></textarea>';
            modal += '                  </div>';
            modal += '                  {!! Form::hidden('enabled', '1', []) !!}';
            modal += '              </div>';
            modal += '              {!! Form::close() !!}';
            modal += '          </div>';
            modal += '          <div class="modal-footer">';
            modal += '              <div class="pull-left">';
            modal += '              {!! Form::button('<span class="fa fa-save"></span> &nbsp;' . trans('general.save'), ['type' => 'button', 'id' =>'button-create-vendor', 'class' => 'btn btn-success']) !!}';
            modal += '              <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-times-circle"></span> &nbsp;{{ trans('general.cancel') }}</button>';
            modal += '              </div>';
            modal += '          </div>';
            modal += '      </div>';
            modal += '  </div>';
            modal += '</div>';

            $('body').append(modal);

            $("#modal-create-vendor #currency_code").select2({
                placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.currencies', 1)]) }}"
            });

            $('#modal-create-vendor').modal('show');
        }

        $(document).on('click', '#button-create-vendor', function (e) {
            $('#modal-create-vendor .modal-header').before('<span id="span-loading" style="position: absolute; height: 100%; width: 100%; z-index: 99; background: #6da252; opacity: 0.4;"><i class="fa fa-spinner fa-spin" style="font-size: 16em !important;margin-left: 35%;margin-top: 8%;"></i></span>');

            $.ajax({
                url: '{{ url("expenses/vendors/vendor") }}',
                type: 'POST',
                dataType: 'JSON',
                data: $("#form-create-vendor").serialize(),
                beforeSend: function () {
                    $('#modal-create-vendor .modal-content').append();

                    $(".form-group").removeClass("has-error");
                    $(".help-block").remove();
                },
                success: function(data) {
                    $('#span-loading').remove();

                    $('#modal-create-vendor').modal('hide');

                    $("#vendor_id").append('<option value="' + data.id + '" selected="selected">' + data.name + '</option>');
                    $("#vendor_id").select2('refresh');
                },
                error: function(error, textStatus, errorThrown) {
                    $('#span-loading').remove();

                    if (error.responseJSON.name) {
                        $("input[name='name']").parent().parent().addClass('has-error');
                        $("input[name='name']").parent().after('<p class="help-block">' + error.responseJSON.name + '</p>');
                    }

                    if (error.responseJSON.email) {
                        $("input[name='email']").parent().parent().addClass('has-error');
                        $("input[name='email']").parent().after('<p class="help-block">' + error.responseJSON.email + '</p>');
                    }

                    if (error.responseJSON.currency_code) {
                        $("select[name='currency_code']").parent().parent().addClass('has-error');
                        $("select[name='currency_code']").parent().after('<p class="help-block">' + error.responseJSON.currency_code + '</p>');
                    }
                }
            });
        });
    </script>
@endpush
