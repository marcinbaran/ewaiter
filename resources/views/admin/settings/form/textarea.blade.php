<input type="hidden" name="value[{{ $key }}]" value="{{ $value }}" />
<x-admin.form.new-input type="textarea" name="value[{{ $key }}]" id="value_active_{{ $loop->index }}" value="{{ $value }}" min="3" max="1000" />
