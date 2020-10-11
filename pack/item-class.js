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

function PL_Rates(exchangeRt, silverRt, goldRt, labourRt, goldLabourRt, platingRt, findingRt, micro, prong, baguette, round){
    this.exchange=Number(exchangeRt) || 0;
    this.silver=Number(silverRt) || 0; 
    this.gold=Number(goldRt) || 0;
    this.labour=Number(labourRt) || 0;
    this.goldLabour=Number(goldLabourRt) || 0;
    this.plating=Number(platingRt) || 0;
    this.findings=Number(findingRt) || 0;
    this.microDiamondSetting = Number(micro) || 0;
    this.prongDiamondSetting = Number(prong) || 0;
    this.baguetteDiamondSetting = Number(baguette) || 0;
    this.roundStoneSetting = Number(round) || 0;
    this.factoryProfit = 10;

}