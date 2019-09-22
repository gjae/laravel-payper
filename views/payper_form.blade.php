<input type="hidden" name="usuario" value="{{ Payper::getPayperUser() }}">
<input type="hidden" name="llavemd5" value="{{ Payper::getPayperMD5Key() }}">
<input type="hidden" name="referencia" value="{{ Payper::getReference() }}">
<input type="hidden" name="moneda"  value="{{ Payper::getPayperCurrency() }}">
<input type="hidden" name="valor" value="{{ Payper::getTotal() }}">
<input type="hidden" name="impuesto"  value="{{ Payper::getTotalTax() }}">
<input type="hidden" name="baseimpuesto" value="{{ Payper::getTotal() - Payper::getTotalTax() }}">
<input type="hidden" name="urlback" value="{{ Payper::getPayperURLBack() }}">
<input type="hidden" name="descripcion" value="{{ Payper::getDescription() }}">