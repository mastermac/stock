function Item(){
    this.id=0;
    this.itemCode='';
    this.mewarCode='';
    this.qty=0;
    this.ringSize='';
    this.metalType='';
    this.metalColor='';
    this.description='';

    this.metalDetails = new Array();
    this.diamondDetails = new Array();
    this.stoneDetails = new Array();
    this.otherDetails= new Array();
}

function ItemMetalDetails(){
    this.grossWt=0;
    this.metalWt=0;
    this.amt=0;
}

function ItemDiamondDetails(){
    this.lotNo=0;
    this.shape='';
    this.size='';
    this.qty=0;
    this.ctWt=0;
    this.rate=0;
    this.amt=0;
}

function ItemStoneDetails(){
    this.lotNo=0;
    this.name='';
    this.shape='';
    this.size='';
    this.qty=0;
    this.ctWt=0;
    this.rate=0;
    this.amt=0;
}

function ItemOtherDetails(){
    this.description='';
    this.amt=0;
}

function PO(po_id, cust_code, entry_date, order_date, ship_date, cancel_date, type, note, total, discount, entered_by, ship_via, customer_ref){
    this.po_id = Number(po_id) || 0;
    this.cust_code = cust_code || "";
    this.entry_date = entry_date;
    this.order_date = order_date;
    this.ship_date = ship_date;
    this.cancel_date = cancel_date;
    this.type = type;
    this.note = note;
    this.total = total;
    this.discount = discount || "";
    this.entered_by = entered_by;
    this.ship_via = ship_via;
    this.customer_ref = customer_ref;
}