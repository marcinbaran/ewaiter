<input type="hidden" name="value[{{ $key }}]" value="{{ $value }}" />
<x-admin.form.new-input type="money" name="value[{{ $key }}]" id="value_active_{{ $loop->index }}" value="{{ $value }}" />
