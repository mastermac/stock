function StoneInventory(lot_no, name, size, shape, seller, purchased_qty, purchased_wt, current_qty, current_wt, unit, box, cost, less, rate, description){
    this.lot_no=lot_no || 0;
    this.name=name || ""; 
    this.size=size || "";
    this.shape=shape || "";
    this.seller=seller || "";
    this.purchased_qty=purchased_qty || 0;
    this.purchased_wt = purchased_wt || 0;
    this.current_qty = current_qty || 0;
    this.current_wt = current_wt || 0;
    this.unit = unit || "cts";
    this.box = box || "";
    this.cost = cost || 0;
    this.less = less*100 || 0;
    this.rate = rate || 0;
    this.description = description || "";
}













