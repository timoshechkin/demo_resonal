
function WinSpr_d_stat(idWin,sel,RowId,CellInd)
{
	//СОЗДАЕМ ОКНО
	
	dhxWins.createWindow(idWin, 10, 10, 600, 400);//ПОЗИЦИЯ И РАЗМЕРЫ ОКНА
	var text = menu.getItemText(idWin);//ЗАГОЛОВОК ОКНА
	dhxWins.window(idWin).setText(text);
	dhxWins.window(idWin).setIcon("table-money.png");
	dhxWins.window(idWin).button("close").attachEvent("onClick", function(){dhxWins.window(idWin).close();dhxList2.uncheckItem("chProf");});
	dhxWins.window(idWin).setModal(true);

	var dhxGrid = dhxWins.window(idWin).attachGrid(); 
    dhxGrid.setImagePath("dhtmlxSuite/dhtmlxGrid/codebase/imgs/");//Путь к папке с иконками
    dhxGrid.setHeader("Код,Таб.,Наименование");//Наименования заголовков
    dhxGrid.setInitWidths("50,50,200");//Ширина столбцов
    dhxGrid.setColAlign("center,center,left");//Добавляет выравнивание в ячейках
    dhxGrid.setSkin(grid_skin);//Оформление
	dhxGrid.setColTypes("ro,ro,ro");//Добавляет формат ячеек и возможность редактирования
    dhxGrid.init();
	dhxGrid.clearAndLoad("windows/sprav/spr_d_stat/getGrid.php");

	dhxGrid.attachEvent("onRowDblClicked", function(rId,cInd)
											{
												if(sel==1)
												{
													var ArrId = Array();
													ArrId = dhxGrid_graf_b.getSelectedRowId().split("_");
													//alert("Месяц:"+ArrId[0]+" День:"+(dhxGrid_graf_b.getSelectedCellIndex()-1)+" id_graf="+dhxGrid_graf_a.getSelectedRowId()+" year="+toolbarWin_graf_b.getItemText("set_year"));
													
													dhtmlxAjax.get("windows/sprav/spr_graf/update_status.php?id_graf="+dhxGrid_graf_a.getSelectedRowId()+"&year="+toolbarWin_graf_b.getItemText("set_year")+"&day="+(dhxGrid_graf_b.getSelectedCellIndex()-1)+"&month="+ArrId[0]+"&status="+dhxGrid.cellById(rId,0).getValue(),
													function(loader)
													{
														var ArrRes = Array();
														ArrRes = loader.xmlDoc.responseText.split("|");
														//alert(loader.xmlDoc.responseText);
														//dhxGrid_graf_b.updateFromXML("windows/sprav/spr_graf/getGrid_b.php?year="+toolbarWin_graf_b.getItemText("set_year")+"&id_graf="+dhxGrid_graf_a.getSelectedRowId(),true,true);
														dhxGrid_graf_b.setCellTextStyle(ArrId[0]+"_status",CellInd,ArrRes[3]);
														dhxGrid_graf_b.cellById(ArrId[0]+"_status",CellInd).setValue(ArrRes[1]);
														dhxGrid_graf_b.cellById(ArrId[0]+"_time",CellInd).setValue(ArrRes[2]);
													});
													dhxWins.window(idWin).close();
												}
											}); 
}

