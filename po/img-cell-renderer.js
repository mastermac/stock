function ImageCellRenderer() {
    this.eGui = document.createElement("span");
}

ImageCellRenderer.prototype.init = function(params) {
  this.value = params.value;
  this.eGui = document.createElement('img');
  this.eGui.src ='pics/'+ this.value + '.JPG';
  this.eGui.width = "75";
  this.eGui.height = "75";
  this.eGui.onerror = function(){
    this.src='pics/noImage.jpeg';
  };
}

ImageCellRenderer.prototype.getGui = function() {
  return this.eGui;
}

ImageCellRenderer.prototype.refresh = function(params) {
    this.value = params.value;
    this.eGui.innerHTML = '';
    this.updateImages();
};
