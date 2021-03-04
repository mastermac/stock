function EditBtnCellRenderer() {}

EditBtnCellRenderer.prototype.init = function(params) {
  this.params = params;
  this.eGui = document.createElement('button');
  this.eGui.innerHTML = '';
  this.eGui.classList.add("btn","btn-warning","waves-effect","compact-btn","btn-rounded");

  this.btnClickedHandler = this.btnClickedHandler.bind(this);
  this.eGui.addEventListener('click', this.btnClickedHandler);
}

EditBtnCellRenderer.prototype.getGui = function() {
  return this.eGui;
}

EditBtnCellRenderer.prototype.destroy = function() {
  this.eGui.removeEventListener('click', this.btnClickedHandler);
}

EditBtnCellRenderer.prototype.btnClickedHandler = function(event) {
  this.params.clicked(this.params.value);
}