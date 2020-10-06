function DelBtnCellRenderer() {}

DelBtnCellRenderer.prototype.init = function(params) {
  this.params = params;
  this.eGui = document.createElement('button');
  this.eGui.innerHTML = '';
  this.eGui.classList.add("btn","btn-danger","waves-effect","compact-btn","btn-rounded");

  this.btnClickedHandler = this.btnClickedHandler.bind(this);
  this.eGui.addEventListener('click', this.btnClickedHandler);
}

DelBtnCellRenderer.prototype.getGui = function() {
  return this.eGui;
}

DelBtnCellRenderer.prototype.destroy = function() {
  this.eGui.removeEventListener('click', this.btnClickedHandler);
}

DelBtnCellRenderer.prototype.btnClickedHandler = function(event) {
  this.params.clicked(this.params.value);
}