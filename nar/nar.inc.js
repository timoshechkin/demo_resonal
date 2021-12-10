function restoreVal(colInd)
{
	if(dhxGridEditListPers.getCheckedRows(0)!="")
	{
		var arr_rowId = Array();
		arr_rowId = dhxGridEditListPers.getCheckedRows(0).split(',');
		
		if(colInd==8)dhxGridEditListPers.setCheckedRows(colInd,0);
		dhxGridEditListPers.setColumnLabel(8,"#master_checkbox",3);
		for($j=0; $j<arr_rowId.length; $j++)
		{
			var old_value = dhxGridEditListPers.cellById(arr_rowId[$j],colInd-1).getValue();
			dhxGridEditListPers.cellById(arr_rowId[$j],colInd).setValue(old_value);
			//Меняем цвет по измененным параметрам
			dhxGridEditListPers.setCellTextStyle(arr_rowId[$j],colInd,"font-family:Tahoma; font-size:11px; color:black;");
		}
	}
	else
	{
		alert("Выберите записи!");
	}
	
}

function WinNar(idWin,sel,oTabN)
{
	var char_code = new Array();
	char_code[81] = "Й";
	char_code[87] = "Ц";
	char_code[69] = "У";
	char_code[82] = "К";
	char_code[84] = "Е";
	char_code[89] = "Н";
	char_code[85] = "Г";
	char_code[73] = "Ш";
	char_code[79] = "Щ";
	char_code[80] = "З";
	char_code[219] = "Х";
	char_code[221] = "Ъ";
	char_code[65] = "Ф";
	char_code[83] = "Ы";
	char_code[68] = "В";
	char_code[70] = "А";
	char_code[71] = "П";
	char_code[72] = "Р";
	char_code[74] = "О";
	char_code[75] = "Л";
	char_code[76] = "Д";
	char_code[186] = "Ж";
	char_code[222] = "Э";
	char_code[90] = "Я";
	char_code[88] = "Ч";
	char_code[67] = "С";
	char_code[86] = "М";
	char_code[66] = "И";
	char_code[78] = "Т";
	char_code[77] = "Ь";
	char_code[188] = "Б";
	char_code[190] = "Ю";

	//СОЗДАЕМ ОКНО
	var dhxWinsPers = dhxWins.createWindow(idWin, 1, 1, 800, 500);//ПОЗИЦИЯ И РАЗМЕРЫ ОКНА
	var text = menu.getItemText(idWin);//ЗАГОЛОВОК ОКНА
	dhxWinsPers.setText(text);
	dhxWinsPers.setIcon("group.png","");
	if(sel==0)dhxWinsPers.maximize(true);
	//ВСТАВЛЯЕМ ТУЛБАР
	winNar_toolbar = dhxWinsPers.attachToolbar();													
	winNar_toolbar.setIconsPath("dhtmlxSuite/dhtmlxToolbar/samples/common/imgs/");
	winNar_toolbar.setSkin(toolbar_skin);

	winNar_toolbar.addText("text_work_date", 0, "Рабочая дата: ");
	winNar_toolbar.addInput("input_date_beg", 1, "", 70);
	winNar_toolbar.setValue("input_date_beg", mCal1.getFormatedDate("%d.%m.%Y"));
	winNar_toolbar.addButton("butCal", 2, "", "calendar.gif", "");
	winNar_toolbar.addSeparator("sep1", 3);
	winNar_toolbar.disableItem("input_date_beg");
	
	winNar_toolbar.attachEvent("onClick", function()
											{
												document.getElementById("modal_background").style.display = "block";
												//Меняем позицию календаря и отображаем его
												var winPos = dhxWinsPers.getPosition();
												mCal1.setPosition(winPos[1]+60, winPos[0]+75);
												mCal1.show();
												
												
											});


	winNar_toolbar.attachEvent("onEnter", function(id, value){dhxGridOtrab2.clearAll();dhxGridOtrab3.clearAll();nar_toolbar_3.disableItem("del");nar_toolbar_3.disableItem("edit");nar_toolbar_3.disableItem("add_copy");dhxNarTree.deleteChildItems(0);dhxNarTree.loadXML("windows/nar/getTree.php?id=0");});
	
	dhxLayout = new dhtmlXLayoutObject(dhxWinsPers, "3L", layout_skin);
	dhxLayout.cells("a").setText("Подразделения");
	dhxLayout.cells("b").setText("Список работников");
	dhxLayout.setEffect('highlight', false);
	var statusbar_b = dhxLayout.cells("b").attachStatusBar();
    statusbar_b.setText("");
	dhxLayout.cells("c").setText("История изменений по сотруднику");
	
	
	dhtmlxAjax.get("windows/nar/getInfo.php?action=layout_size",
				function(loader)
					{
						var ArrayRes = new Array();
						ArrayRes = loader.xmlDoc.responseText.split('|');

						dhxLayout.cells("a").setWidth(ArrayRes[1]);
						dhxLayout.cells("c").setHeight(ArrayRes[2]);
						
						dhxLayout.attachEvent("onPanelResizeFinish",
								function()
									{
										dhtmlxAjax.post("windows/user_settings/save.php","action=update_layout_size_pers&PERS_A_W="+dhxLayout.cells("a").getWidth()+"&PERS_C_H="+dhxLayout.cells("c").getHeight(),function(loader){});
									});
					});

	dhxNarTree = dhxLayout.cells("a").attachTree();
	dhxNarTree.setImagePath("dhtmlxSuite/dhtmlxTree/codebase/imgs/"+tree_icons+"/");
	dhxNarTree.setSkin(tree_skin);
	dhxNarTree.setXMLAutoLoading("windows/nar/getTree.php");
    dhxNarTree.loadXML("windows/nar/getTree.php?id=0");
	dhxNarTree.attachEvent("onSelect", function(id){
											if (nar_toolbar.getItemState("filtr_uvolen")==true) {uvolen = 1;} else {uvolen = '';}
											if (nar_toolbar.getItemState("filtr_dekret")==true) {dekret = 1;} else {dekret = '';}
											//WinInfo("Обновление списка сотрудников...");
											//dhxLayout.cells("b").progressOn();
											dhxLayout.cells("b").progressOn();
											dhxGridOtrab2.clearAndLoad("windows/nar/getGrid2.php?filtr_uvolen="+uvolen+"&filtr_dekret="+dekret+"&date="+winNar_toolbar.getValue("input_date_beg")+"&idSel="+id+"&listIdChild="+dhxNarTree.getAllSubItems(id));
											dhxGridOtrab3.clearAll();
											nar_toolbar_3.disableItem("del");
											nar_toolbar_3.disableItem("edit");
											nar_toolbar_3.disableItem("add_copy");
											 });
	
	dhxNarTree.attachEvent("onXLE", function(dhxNarTree,id){dhxNarTree.openAllItems(1);});
	
	
	
		
	var iii;
	function sendEnd()
	{
		iii++;
		if(iii==2) {/*dhxWinInfo.close();*/ alert("Проверка завершена!");}
	}
	
	function WinCheckPerson(id)
		{
			var date = new Date();
			var dhxWinsCheckPerson = dhxWins.createWindow(id, 100, 80, 500, 400);
			dhxWinsCheckPerson.denyResize();
			dhxWinsCheckPerson.denyPark();
			dhxWinsCheckPerson.setIcon("about.gif","");
			dhxWinsCheckPerson.setText("Сверка с 1С на дату: "+winNar_toolbar.getValue("input_date_beg")+" г.");
			
			var dhxLayoutCheckPerson = new dhtmlXLayoutObject(dhxWinsCheckPerson, "2E", layout_skin);
			dhxLayoutCheckPerson.cells("a").hideHeader();
			dhxLayoutCheckPerson.cells("b").setText("Ошибки:");
			dhxLayoutCheckPerson.cells("a").setHeight(100);
			dhxLayoutCheckPerson.cells("a").fixSize(true, true);
			
			var div = document.createElement('div');
			div.innerHTML = "<iframe style='display:none; width:477px; height:222px' scr='about:blank' id='pole' name='pole' onload='sendEnd()'></iframe>";
			document.body.appendChild(div);
			
			dhxLayoutCheckPerson.cells("b").attachObject("pole");
			
			
			var str = "<form id='form_check_1c' enctype='multipart/form-data' method='post' action='windows/nar/checkPeson.php' target='pole'></form>";
			dhxLayoutCheckPerson.cells("a").attachHTMLString(str);
			var Data = [{
							type: "input",
							name: "date",
    						width: 80,
							value: winNar_toolbar.getValue("input_date_beg")
						},{
							type: "label",
							label: "Путь к файлу из 1С в формате Excel:"
						},{
							type: "file",
							name: "file",
    						width: 200
						},{
    						type: "button", 
							name: "start", 
							value: "Выполнить", 
							command: "customCommand"
						}];

			var dhxForm = new dhtmlXForm("form_check_1c",Data);
			
			dhxForm.hideItem("date");
			
			dhxForm.attachEvent("onButtonClick", function(name, command){
														  					if(name=="start")
																			{
																				iii=0;
																				document.getElementById("form_check_1c").submit();
																				//dhxWinsCheckPerson.close();
																				//WinInfo("Сверка персонала...");
																			};
														  				});
			
			
			
		}
	
	nar_toolbar = dhxLayout.cells("b").attachToolbar();	
	nar_toolbar.setSkin(toolbar_skin);
	nar_toolbar.setIconsPath("dhtmlxSuite/dhtmlxToolbar/samples/common/imgs/");
	nar_toolbar.addSeparator("sep0", 5);
	nar_toolbar.addButton("new", 10, "Добавить нового сотрудника","new.gif","new_dis.gif");
	nar_toolbar.addSeparator("sep1", 15);
	nar_toolbar.addButton("edit_list", 20, "Массовое редактирование","edit.gif","edit_dis.gif");
	nar_toolbar.addSeparator("sep2", 25);
	nar_toolbar.addButtonTwoState("filtr_uvolen", 30, "Показать уволенных", "", "");
	nar_toolbar.addSeparator("sep3", 35);
	nar_toolbar.setItemToolTip("filtr_uvolen", "Показать уволенных");
	nar_toolbar.addButtonTwoState("filtr_dekret", 40, "Показать декретников", "", "");
	nar_toolbar.addSeparator("sep4", 45);
	nar_toolbar.setItemToolTip("filtr_dekret", "Показать декретников");
	nar_toolbar.addButton("check", 50, "Выполнить сверку с 1С","about.gif","about_dis.gif");
	nar_toolbar.addSeparator("sep5", 55);
	nar_toolbar.addButton("to_excel", 60, "Выгрузить список в Excel","page_excel.png","page_excel_dis.png");
	nar_toolbar.addSeparator("sep6", 65);
	nar_toolbar.addButton("but_list_ds", 70, "Список доп. соглашений ЗП", "table_multiple.png", "");
	nar_toolbar.addSeparator("sep7", 75);
	nar_toolbar.attachEvent("onStateChange", function(idState, state){
																		if(idState=="filtr_uvolen")
																		{
																			if(state==true){uvolen = '1';} else {uvolen = '';}
																			if(nar_toolbar.getItemState("filtr_dekret")==true){dekret = '1';} else {dekret = '';}
																			//WinInfo("Обновление списка сотрудников...");
																			dhxLayout.cells("b").progressOn();
																			dhxGridOtrab2.clearAndLoad("windows/nar/getGrid2.php?filtr_uvolen="+uvolen+"&filtr_dekret="+dekret+"&date="+winNar_toolbar.getValue("input_date_beg")+"&idSel="+dhxNarTree.getSelectedItemId()+"&listIdChild="+dhxNarTree.getAllSubItems(dhxNarTree.getSelectedItemId()));
																			dhxGridOtrab3.clearAll();
																			nar_toolbar_3.disableItem("del");
																			nar_toolbar_3.disableItem("edit");
																			nar_toolbar_3.disableItem("add_copy");
																		}
																		if(idState=="filtr_dekret")
																		{
																			if(state==true){dekret = '1';} else {dekret = '';}
																			if(nar_toolbar.getItemState("filtr_uvolen")==true){uvolen = '1';} else {uvolen = '';}
																			//WinInfo("Обновление списка сотрудников...");
																			dhxLayout.cells("b").progressOn();
																			dhxGridOtrab2.clearAndLoad("windows/nar/getGrid2.php?filtr_uvolen="+uvolen+"&filtr_dekret="+dekret+"&date="+winNar_toolbar.getValue("input_date_beg")+"&idSel="+dhxNarTree.getSelectedItemId()+"&listIdChild="+dhxNarTree.getAllSubItems(dhxNarTree.getSelectedItemId()));
																			dhxGridOtrab3.clearAll();
																			nar_toolbar_3.disableItem("del");
																			nar_toolbar_3.disableItem("edit");
																			nar_toolbar_3.disableItem("add_copy");
																		}
														});
	
	nar_toolbar.attachEvent("onClick", function(idBut){
														if(idBut=="new" && dhxNarTree.getSelectedItemId() != ""){/*alert(dhxNarTree.getSelectedItemId()); */winEditPers("new",dhxNarTree.getSelectedItemId());}
														if(idBut=="edit_list"){WinEditListPers(idBut);}
														if(idBut=="check") WinCheckPerson("CheckPerson");
														if(idBut=="to_excel" && confirm("Выгрузить список работников на "+winNar_toolbar.getValue("input_date_beg")+" г. в Excel?"))
														{
															statusbar_b.setText("Выгрузка в Excel...");
															dhtmlxAjax.get("windows/nar/toExcel.php?action="+idBut+"&date="+winNar_toolbar.getValue("input_date_beg"), 
																	function(loader)
																	{
																		//alert("Ok");
																		statusbar_b.setText("");
																		var win_load = window.open("http://webk05/windows/nar/reports/nar_"+winNar_toolbar.getValue("input_date_beg")+".xls");
																	});
														}
														if(idBut=="but_list_ds")
														{
															WinDs(idBut);
														}
														});
												
												
	dhxGridOtrab2 = dhxLayout.cells("b").attachGrid(); 
	dhxGridOtrab2.setImagePath("dhtmlxSuite/dhtmlxGrid/codebase/imgs/");//Путь к папке с иконками
	dhxGridOtrab2.setHeader("Таб.,Фамилия,Имя,Отчество,Подр.,Должность,Ставка,Категория,Режим учета рабоч. врем.,{#collapse}3:График и режим работы,Уволен,Декрет,Дата начала,Дата окончан.");//Наименования заголовков
	dhxGridOtrab2.attachHeader("#select_filter_strict,#rspan,#rspan,#rspan,#select_filter_strict,#select_filter_strict,#select_filter_strict,#select_filter_strict,#select_filter_strict,#select_filter_strict,#rspan,#rspan,#select_filter_strict,#select_filter_strict");
	dhxGridOtrab2.setInitWidths("45,100,100,100,65,*,50,100,110,*,70,70,70,70");//Ширина столбцов
	dhxGridOtrab2.setColTypes("ro,ed,ro,ro,ro,ro,ro,ro,ro,ro,img,img,ro,ro");
	dhxGridOtrab2.setColSorting("str,str,str,str,str,str,str,str,str,str,str,str,str,str");
	dhxGridOtrab2.setColAlign("center,left,left,left,center,left,center,left,left,left,center,center,center,center");
	dhxGridOtrab2.enableTooltips("false,false,false,false,false,true,false,false,false,true,false,false,false,false");
	dhxGridOtrab2.setImagePath("dhtmlxSuite/dhtmlxGrid/codebase/imgs/");
	dhxGridOtrab2.setSkin(grid_skin);//Оформление
	dhxGridOtrab2.attachEvent("onKeyPress", function(code,cFlag,sFlag)
											{
												//alert(code);
												if(char_code[code])
												{
													var new_str = statusbar_b.getText()+char_code[code];
													statusbar_b.setText(new_str);
													
													dhtmlxAjax.get("windows/nar/search.php?str_fam="+statusbar_b.getText()+"&date="+winNar_toolbar.getValue("input_date_beg"),
															function(loader)
																{
																	var ArrayRes = new Array();
																	ArrayRes = loader.xmlDoc.responseText.split('|');
																	//alert(ArrayRes[1]);
																	if(ArrayRes[1]=="0")
																	{
																		dhxGridOtrab2.clearSelection();
																		dhxGridOtrab3.clearAll();
																	}
																	else
																	{
																		dhxGridOtrab2.selectCell(dhxGridOtrab2.getRowIndex(ArrayRes[1]),1);
																		dhxGridOtrab3.clearAndLoad("windows/nar/getGrid3.php?id="+ArrayRes[1]+"&date="+winNar_toolbar.getValue("input_date_beg"));
																	}
																	
																});
													
													
												}
												else
												{
													return true;
												}
											});

	dhxGridOtrab2.attachEvent("onRowDblClicked", function(rId,cInd)
												{
													//if(sel==0) winEditPers("edit",rId);
													if(sel==1){ dhxGridPrich_otkl.cellById(dhxGridPrich_otkl.getSelectedRowId(),1).setValue(dhxGridOtrab2.cellById(rId,0).getValue()); if(dhxGridOtrab2.cellById(rId,0).getValue()!=oTabN) {toolbarPrich_otkl.enableItem("save"); dhxGridPrich_otkl.cellById(dhxGridPrich_otkl.getSelectedRowId(),0).setValue("dhtmlxSuite/dhtmlxGrid/codebase/imgs/red.gif");} dhxWins.window(idWin).close();}
													if(sel==2){ if(dhxGrid_Users.cellById(dhxGrid_Users.getSelectedRowId(),0).getValue()==""){dhxGrid_Users.addRow(dhxGrid_Users.uid(),["","","","","",0,0]);} dhxGrid_Users.cellById(dhxGrid_Users.getSelectedRowId(),0).setValue(dhxGridOtrab2.cellById(rId,0).getValue()); dhxGrid_Users.cellById(dhxGrid_Users.getSelectedRowId(),1).setValue(dhxGridOtrab2.cellById(rId,1).getValue()); dhxGrid_Users.cellById(dhxGrid_Users.getSelectedRowId(),2).setValue(dhxGridOtrab2.cellById(rId,2).getValue());dhxGrid_Users.cellById(dhxGrid_Users.getSelectedRowId(),3).setValue(dhxGridOtrab2.cellById(rId,3).getValue());dhxWins.window(idWin).close();}
												});
	dhxGridOtrab2.attachEvent("onRowSelect", function(rId,cInd)
												{
													dhxGridOtrab3.clearAndLoad("windows/nar/getGrid3.php?id="+rId+"&date="+winNar_toolbar.getValue("input_date_beg"));
													nar_toolbar_3.disableItem("del");
													nar_toolbar_3.disableItem("edit");
													nar_toolbar_3.disableItem("add_copy");
													statusbar_b.setText("");
												});
	dhxGridOtrab2.attachEvent("onXLE", function(grid_obj,count){dhxLayout.cells("b").progressOff();});
	dhxGridOtrab2.init();
	dhxGridOtrab2.collapseColumns(8);


	nar_toolbar_3 = dhxLayout.cells("c").attachToolbar();
	nar_toolbar_3.setSkin(toolbar_skin);
	nar_toolbar_3.setIconsPath("dhtmlxSuite/dhtmlxToolbar/samples/common/imgs/");
	nar_toolbar_3.addSeparator("sep1", 10);
	nar_toolbar_3.addButton("del", 20, "Удалить","delete.gif","delete_dis.gif");
	nar_toolbar_3.addSeparator("sep2", 30);
	nar_toolbar_3.disableItem("del");
	nar_toolbar_3.addButton("edit", 40, "Редактировать","edit.gif","edit_dis.gif");
	nar_toolbar_3.addSeparator("sep3", 50);
	nar_toolbar_3.disableItem("edit");
	nar_toolbar_3.addButton("add_copy", 60, "Добавить после выделенной","new.gif","new_dis.gif");
	nar_toolbar_3.disableItem("add_copy");
	nar_toolbar_3.addSeparator("sep4", 70);
	
	nar_toolbar_3.attachEvent("onClick", function(idBut)
											{
												if(idBut=="del" && dhxGridOtrab3.getSelectedRowId() != null && confirm("Вы действительно хотите удалить запись?"))
														{
															dhtmlxAjax.get("windows/nar/savePers.php?action=delete&id="+dhxGridOtrab3.getSelectedRowId(),
															function(loader)
																{
																	if (nar_toolbar.getItemState("filtr_uvolen")==true) {uvolen = 1;} else {uvolen = '';}
																	if (nar_toolbar.getItemState("filtr_dekret")==true) {dekret = 1;} else {dekret = '';}
																	dhxGridOtrab3.clearAndLoad("windows/nar/getGrid3.php?tabn="+dhxGridOtrab2.cellById(dhxGridOtrab2.getSelectedRowId(),0).getValue()+"&date="+winNar_toolbar.getValue("input_date_beg"));
																	//WinInfo("Обновление списка сотрудников...");
																	dhxLayout.cells("b").progressOn();
																	dhxGridOtrab2.clearAndLoad("windows/nar/getGrid2.php?tabn="+dhxGridOtrab2.cellById(dhxGridOtrab2.getSelectedRowId(),0).getValue()+"&filtr_uvolen="+uvolen+"&filtr_dekret="+dekret+"&date="+winNar_toolbar.getValue("input_date_beg")+"&idSel="+dhxNarTree.getSelectedItemId()+"&listIdChild="+dhxNarTree.getAllSubItems(dhxNarTree.getSelectedItemId()));
																	nar_toolbar_3.disableItem("del");
																	nar_toolbar_3.disableItem("edit");
																	nar_toolbar_3.disableItem("add_copy");
																	//dhxGridOtrab2.clearAndLoad("windows/nar/getGrid2.php?date="+winNar_toolbar.getValue("input_date_beg")+"&idSel="+dhxNarTree.getSelectedItemId()+"&listIdChild="+dhxNarTree.getAllSubItems(dhxNarTree.getSelectedItemId()));
																	
																});
														}
												if(idBut=="edit" && dhxGridOtrab3.getSelectedRowId() != null)
														{
															//alert(dhxGridOtrab3.getSelectedRowId());
															winEditPers("edit",dhxGridOtrab3.getSelectedRowId());
														}
												if(idBut=="add_copy" && dhxGridOtrab3.getSelectedRowId() != null)
														{
															//alert(dhxGridOtrab3.getSelectedRowId());
															winEditPers("add_copy",dhxGridOtrab3.getSelectedRowId());
														}
											});
											
	
	
	dhxGridOtrab3 = dhxLayout.cells("c").attachGrid(); 
	dhxGridOtrab3.setHeader("Таб.,Фамилия,Имя,Отчество,Подр.,Должность,Ставка,Категория,Режим учета рабоч. врем.,{#collapse}3:График и режим работы,Уволен,Декрет,Дата начала,Дата окончан.");//Наименования заголовков
	
	dhxGridOtrab3.setInitWidths("45,100,100,100,65,*,50,100,110,*,70,70,70,70");//Ширина столбцов
	dhxGridOtrab3.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,img,img,ro,ro");
	dhxGridOtrab3.setColAlign("center,left,left,left,center,left,center,left,left,left,center,center,center,center");
	dhxGridOtrab3.enableTooltips("false,false,false,false,false,true,false,false,false,true,false,false,false,false");
	dhxGridOtrab3.setImagePath("dhtmlxSuite/dhtmlxGrid/codebase/imgs/");
	dhxGridOtrab3.setSkin(grid_skin);//Оформление
	dhxGridOtrab3.init();
	dhxGridOtrab3.collapseColumns(8);
	dhxGridOtrab3.attachEvent("onRowSelect", function(rId,cInd)
												{
													nar_toolbar_3.enableItem("del");
													nar_toolbar_3.enableItem("edit");
													nar_toolbar_3.enableItem("add_copy");
												});
	dhxGridOtrab3.attachEvent("onRowDblClicked", function(rId,cInd)
												{
													if(sel==0) winEditPers("edit",rId);
												});
	dhxGridOtrab3.attachEvent("onXLE", function(grid_obj,count){dhxGridOtrab2.selectCell(dhxGridOtrab2.getRowIndex(dhxGridOtrab2.getSelectedRowId()),dhxGridOtrab2.getSelectedCellIndex());});
	
	function winEditPers(idBut,id)
		{

			//var idRoot,text,date_beg,date_end,date_end_graf,tabn,fam,name,otch,id_podr,podrazd,profes,kateg,propusk,osn_rejim,dop_rejim,rabota,dekret,oklad,nadbavka,proc_prem;
			var ArrayParam = new Array();
			
			var dhxWinsEditPers = dhxWins.createWindow(id,200,10,700,780);
			dhxWinsEditPers.setIcon("cog.png","");
			//dhxWinsEditPers.denyResize();
			dhxWinsEditPers.denyPark();
			//dhxWins.window(id).button("close").attachEvent("onClick", function(){winClose();});
			dhxWinsEditPers.button("minmax1").hide();
			dhxWinsEditPers.button("park").hide();
			dhxWinsEditPers.button("close").hide();
			dhxWinsEditPers.setModal(true);
			
			
			
			toolbarWinEditPers = dhxWinsEditPers.attachToolbar();
			toolbarWinEditPers.setSkin(toolbar_skin);
			toolbarWinEditPers.setIconsPath("dhtmlxSuite/dhtmlxToolbar/samples/common/imgs/");
			//toolbarWinEditPers.setItemText("toolbarWinEditPersText_1", "Период действия с:");
			toolbarWinEditPers.addText("toolbarWinEditPersText_1", 5, "Период с:");
			toolbarWinEditPers.addInput("date_beg", 10, "", 70);
			toolbarWinEditPers.disableItem("date_beg");
			toolbarWinEditPers.addButton("butCal_beg", 20, "", "calendar.gif", "calendar_dis.gif");
			//toolbarWinEditPers.addSeparator("sep1", 30);
			toolbarWinEditPers.addText("toolbarWinEditPersText_2", 35, "по:");
			toolbarWinEditPers.addInput("date_end", 40, "", 70);
			toolbarWinEditPers.disableItem("date_end");
			//toolbarWinEditPers.addButton("butCal_end", 50, "", "calendar.gif", "");
			toolbarWinEditPers.addSeparator("sep2", 60);
			
			toolbarWinEditPers.addSpacer("sep2");
			
			toolbarWinEditPers.addSeparator("sep3", 65);
			toolbarWinEditPers.addButton("save", 70, "Сохранить и закрыть","save.png","");
			toolbarWinEditPers.setItemToolTip("save", "Сохранить и закрыть");
			toolbarWinEditPers.addSeparator("sep4", 80);
			
			toolbarWinEditPers.addButton("close", 90, "Закрыть","delete.gif","");
			toolbarWinEditPers.setItemToolTip("close", "Закрыть");
			//toolbarWinEditPers.addSeparator("sep5", 100);
			
			toolbarWinEditPers.attachEvent("onClick", function(idButPers)
														{
															if(idButPers=="butCal_beg"){var winPos = dhxWinsEditPers.getPosition(); mCal_pers_beg.setPosition(winPos[1]+80, winPos[0]+75); mCal_pers_end.hide(); mCal_pers_beg.show(); document.getElementById("modal_background").style.display = "block";}
															if(idButPers=="butCal_end"){var winPos = dhxWinsEditPers.getPosition(); mCal_pers_end.setPosition(winPos[1]+80, winPos[0]+210); mCal_pers_beg.hide(); mCal_pers_end.show(); document.getElementById("modal_background").style.display = "block";}
															if(idButPers=="close"){dhxWinsEditPers.close();}

															if(idButPers=="save" && confirm("Сохранить внесенные изменения по сотруднику?"))//Сохраняем параметры сотрудника
															{
																
																//if(ArrayParam[2]!=toolbarWinEditPers.setValue("date_beg")) var massage = "Начальная дата изменена"
																if(dhxListNar2.isItemChecked("nenorm")) var nenorm=1; else var nenorm="";
																if(dhxListNar5.isItemChecked("uvolen")) var uvolen=1; else var uvolen="";
																if(dhxListNar5.isItemChecked("dekret")) var dekret=1; else var dekret="";
																if(dhxListNar5.isItemChecked("osn_td")) var osn_td=1; else var osn_td="";
																if(dhxListNar5.isItemChecked("osn_ds")) var osn_ds=1; else var osn_ds="";
																if(dhxListNar5.isItemChecked("usl_trud")) var usl_trud=1; else var usl_trud="";
																if(dhxListNar5.isItemChecked("reg_trud_otd")) var reg_trud_otd=1; else var reg_trud_otd="";
																if(dhxListNar5.isItemChecked("usl_opl")) var usl_opl=1; else var usl_opl="";
																//alert(nenorm);
																
																dhtmlxAjax.get("windows/nar/savePers.php?action="+dhxListNar1.getItemValue("action")+
																"&id="+dhxListNar1.getItemValue("id")+
																"&tabn="+dhxListNar1.getItemValue("tabn")+
																"&date_beg="+toolbarWinEditPers.getValue("date_beg")+
																"&date_end="+toolbarWinEditPers.getValue("date_end")+
																"&fam="+dhxListNar1.getItemValue("fam")+
																"&fam_rod="+dhxListNar1.getItemValue("fam_rod")+
																"&name="+dhxListNar1.getItemValue("name")+
																"&otch="+dhxListNar1.getItemValue("otch")+
																"&id_podrazd="+dhxListNar1.getItemValue("id_podrazd")+
																"&id_profes="+dhxListNar1.getItemValue("id_profes")+
																"&id_kateg="+dhxListNar1.getItemValue("id_kateg")+
																"&id_prop="+dhxListNar2.getItemValue("id_prop")+
																"&id_graf="+dhxListNar2.getItemValue("id_graf")+
																"&date_end_graf="+dhxListNar2.getItemValue("date_end_graf")+
																"&stavka="+dhxListNar2.getItemValue("stavka")+
																"&nenorm="+nenorm+
																"&uvolen="+uvolen+
																"&dekret="+dekret+
																"&oklad="+dhxListNar3.getItemValue("oklad")+
																"&nadbavka="+dhxListNar3.getItemValue("nadbavka")+
																"&proc_prem="+dhxListNar3.getItemValue("proc_prem")+
																"&dopl_sovm="+dhxListNar3.getItemValue("dopl_sovm")+
																"&proc_dopl_secret="+dhxListNar3.getItemValue("proc_dopl_secret")+
																"&proc_dopl_vred="+dhxListNar3.getItemValue("proc_dopl_vred")+
																"&proc_dopl_klass="+dhxListNar3.getItemValue("proc_dopl_klass")+
																"&dopl_molod_spec="+dhxListNar3.getItemValue("dopl_molod_spec")+
																"&proc_rk="+dhxListNar3.getItemValue("proc_rk")+
																"&ip="+dhxListNar4.getItemValue("ip")+
																"&profile="+dhxListNar4.getItemValue("profile")+
																"&list_id_podr_drive="+dhxGridEditPersTab_4.getAllRowIds()+
																"&id_otr_bux="+dhxListNar3.getItemValue("id_otr_bux")+
																"&num_td="+dhxListNar5.getItemValue("num_td")+
																"&date_td="+dhxListNar5.getItemValue("date_td")+
																"&osn_td="+osn_td+
																"&vid_ds="+dhxListNar5.getItemValue("vid_ds")+
																"&num_ds="+dhxListNar5.getItemValue("num_ds")+
																"&date_ds="+dhxListNar5.getItemValue("date_ds")+
																"&osn_ds="+osn_ds+
																"&date_rogd="+dhxListNar6.getItemValue("date_rogd")+
																"&pol="+dhxListNar6.getItemValue("pol")+
																"&ser_pasp="+dhxListNar6.getItemValue("ser_pasp")+
																"&num_pasp="+dhxListNar6.getItemValue("num_pasp")+
																"&kem_vid_pasp="+dhxListNar6.getItemValue("kem_vid_pasp")+
																"&date_vid_pasp="+dhxListNar6.getItemValue("date_vid_pasp")+
																"&kod_podr_pasp="+dhxListNar6.getItemValue("kod_podr_pasp")+
																"&adres_propis="+dhxListNar6.getItemValue("adres_propis")+
																"&id_vid_obraz="+dhxListNar6.getItemValue("id_vid_obraz")+
																"&date_end_obraz="+dhxListNar6.getItemValue("date_end_obraz")+
																"&id_prich_izm="+dhxListNar5.getItemValue("prich_izm")+
																"&izm_razd_5="+usl_trud+
																"&izm_razd_6="+reg_trud_otd+
																"&izm_razd_7="+usl_opl+
																"&date_end_izm_5="+dhxListNar5.getItemValue("date_end_usl_trud")+
																"&date_end_izm_6="+dhxListNar5.getItemValue("date_end_reg_trud_otd")+
																"&date_end_izm_7="+dhxListNar5.getItemValue("date_end_usl_opl"),
																	function(loader)
																	{
																		//alert(loader.xmlDoc.responseText);
																		if (nar_toolbar.getItemState("filtr_uvolen")==true) {uvolen = 1;} else {uvolen = '';}
																		if (nar_toolbar.getItemState("filtr_dekret")==true) {dekret = 1;} else {dekret = '';}
																		if (dhxListNar1.getItemValue("action")=="edit" || dhxListNar1.getItemValue("action")=="add_copy")
																		{
																			dhxLayout.cells("b").progressOn();
																			dhxGridOtrab2.clearAndLoad("windows/nar/getGrid2.php?tabn="+dhxGridOtrab2.cellById(dhxGridOtrab2.getSelectedRowId(),0).getValue()+"&filtr_uvolen="+uvolen+"&filtr_dekret="+dekret+"&date="+winNar_toolbar.getValue("input_date_beg")+"&idSel="+dhxNarTree.getSelectedItemId()+"&listIdChild="+dhxNarTree.getAllSubItems(dhxNarTree.getSelectedItemId())+"&tabn="+dhxListNar1.getItemValue("tabn"));
																			dhxGridOtrab3.clearAndLoad("windows/nar/getGrid3.php?tabn="+dhxListNar1.getItemValue("tabn")+"&date="+winNar_toolbar.getValue("input_date_beg"));
																			nar_toolbar_3.disableItem("del");
																			nar_toolbar_3.disableItem("edit");
																			nar_toolbar_3.disableItem("add_copy");
																			
																		}
																		else
																		{
																			//dhxGridOtrab3.clearAll();
																			dhxLayout.cells("b").progressOn();
																			dhxGridOtrab2.clearAndLoad("windows/nar/getGrid2.php?filtr_uvolen="+uvolen+"&filtr_dekret="+dekret+"&date="+winNar_toolbar.getValue("input_date_beg")+"&idSel="+dhxNarTree.getSelectedItemId()+"&listIdChild="+dhxNarTree.getAllSubItems(dhxNarTree.getSelectedItemId())+"&tabn="+dhxListNar1.getItemValue("tabn"));
																			dhxGridOtrab3.clearAndLoad("windows/nar/getGrid3.php?tabn="+dhxListNar1.getItemValue("tabn")+"&date="+winNar_toolbar.getValue("input_date_beg"));
																			nar_toolbar_3.disableItem("del");
																			nar_toolbar_3.disableItem("edit");
																			nar_toolbar_3.disableItem("add_copy");
																		}
																		dhxWinsEditPers.close();
																		
																	});
																
															}
														});
			
			tabbarEditPers = dhxWinsEditPers.attachTabbar();
			tabbarEditPers.setSkin(tabbar_skin);
			tabbarEditPers.setImagePath("dhtmlxSuite/dhtmlxTabbar/codebase/imgs/");
			tabbarEditPers.addTab("a1", "Общее", "100px",0,0);
			tabbarEditPers.addTab("a5", "Трудовой договор", "130px",1,0);
			tabbarEditPers.addTab("a2", "Режим", "100px",2,0);
			tabbarEditPers.addTab("a3", "Оплата", "100px",3,0);
			tabbarEditPers.addTab("a6", "Персон. данные", "120px",4,0);
			tabbarEditPers.addTab("a4", "Доступ", "100px",5,0);
			
			
			tabbarEditPers.setTabActive("a1");
			
			dhxLayoutEditPersTab_4 = tabbarEditPers.cells("a4").attachLayout("2E", layout_skin);
			dhxLayoutEditPersTab_4.cells("a").hideHeader();
			dhxLayoutEditPersTab_4.cells("a").setHeight(120);
			dhxLayoutEditPersTab_4.cells("a").fixSize(true, true);
			dhxLayoutEditPersTab_4.cells("b").setText("Список доступных подразделений");
			
			
			var toolbarWinEditPersTab_4 = dhxLayoutEditPersTab_4.cells("b").attachToolbar();
			toolbarWinEditPersTab_4.setSkin(toolbar_skin);
			toolbarWinEditPersTab_4.setIconsPath("dhtmlxSuite/dhtmlxToolbar/samples/common/imgs/");
			toolbarWinEditPersTab_4.addButton("add_podr", 10, "Добавить", "new.gif", "new_dis.gif");
			toolbarWinEditPersTab_4.disableItem("add_podr");
			toolbarWinEditPersTab_4.addSeparator("sep1", 15);
			toolbarWinEditPersTab_4.addButton("del_podr", 20, "Удалить", "delete.gif", "delete_dis.gif");
			toolbarWinEditPersTab_4.disableItem("del_podr");
			toolbarWinEditPersTab_4.attachEvent("onClick", function(idBut)
															{
																if(idBut=="add_podr")
																{
																	WinSpr_podrazd("spr_podrazd","WinEditPersTab_4","");
																}
																if(idBut=="del_podr")
																{
																	dhxGridEditPersTab_4.deleteSelectedRows();
																}
															});
			
			
			
			
			
			dhxGridEditPersTab_4 = dhxLayoutEditPersTab_4.cells("b").attachGrid(); 
			dhxGridEditPersTab_4.setHeader("Подразделения");//Наименования заголовков
			dhxGridEditPersTab_4.setInitWidths("*");//Ширина столбцов
			dhxGridEditPersTab_4.setColTypes("ro");
			dhxGridEditPersTab_4.setColAlign("left");
			dhxGridEditPersTab_4.enableTooltips("false");
			dhxGridEditPersTab_4.setImagePath("dhtmlxSuite/dhtmlxGrid/codebase/imgs/");
			dhxGridEditPersTab_4.setSkin(grid_skin);//Оформление
			dhxGridEditPersTab_4.enableMultiselect(true);
			dhxGridEditPersTab_4.init();
			dhxGridEditPersTab_4.clearAndLoad("windows/nar/getGridPodr.php?id="+id);
			
			
			function winClose()
				{
					mCal_pers_beg.hide();
					mCal_pers_end.hide();
					mCal4.hide();
					dhxWinsEditPers.close();
				}

			var a1Data = [{
							type: "input",
							name: "action",
    						width: 80,
							value: idBut
						}, {
							type: "input",
							name: "id",
    						width: 80,
							value: id
						}, {
    						type: "label",
    						label: "Табельный №:"
						}, {
							type: "input",
							name: "tabn",
    						width: 80
						}, {
    						type: "label",
    						label: "Фамилия:"
						}, {
    						type: "input",
							label: "Именительный падеж:",
							name: "fam",
    						width: 200
						}, {
    						type: "input",
							label: "Родительный падеж:",
							name: "fam_rod",
    						width: 200
						}, {
    						type: "label",
    						label: "Имя:"
						}, {
							type: "input",
							name: "name",
    						width: 200
						}, {
    						type: "label",
    						label: "Отчество:"
						}, {
							type: "input",
							name: "otch",
    						width: 200
						}, {
    						type: "label",
    						label: "Подразделение:"
						}, {
							type: "input",
							name: "id_podrazd",
    						width: 20
						}, {
							type: "input",
							name: "podrazd",
    						width: 500
						}, {
							type: "button",
							name: "sel_podrazd",
							value: "Выбрать"
						}, {
    						type: "label",
    						label: "Должность / профессия:"
						}, {
							type: "input",
							name: "id_profes",
    						width: 20
						}, {
							type: "input",
							name: "profes",
    						width: 500
						}, {
							type: "button",
							name: "sel_profes",
							value: "Выбрать"
						}, {
    						type: "label",
    						label: "Категория персонала:"
						}, {
							type: "input",
							name: "id_kateg",
    						width: 20
						}, {
							type: "input",
							name: "kateg",
    						width: 350
						}, {
							type: "button",
							name: "sel_kateg",
							value: "Выбрать"
						}
						];
			
			var a2Data = [{
    						type: "label",
    						label: "Режим контроля и учета рабочего времени:"
						}, {
							type: "input",
							name: "id_prop",
    						width: 20
						}, {
							type: "input",
							name: "propusk",
    						width: 350
						}, {
							type: "button",
							name: "sel_propusk",
							value: "Выбрать"
						}, {
    						type: "label",
    						label: "График и режим работы:"
						}, {
							type: "input",
							name: "id_graf",
    						width: 50
						}, {
							type: "input",
							name: "graf",
    						width: 450
						}, {
							type: "button",
							name: "sel_graf",
							value: "Выбрать"
						}, {
    						type: "label",
    						label: "Занимаемая ставка (ед.):"
						}, {
							type: "input",
							name: "stavka",
    						width: 30
						}, {
							type: "input",
							name: "date_end_graf",
    						width: 80,
							style: "background-color: #EEEEEE; color:red; "
						}, {
							type: "button",
							name: "sel_date_end_graf",
							value: "Выбрать"
						}, {
    						type: "label",
    						label: "Статус ненормированного рабочего дня:"
						}, {
    						type: "checkbox",
							name: "nenorm",
    						label: "Не установлен",
							value: 1,
							checked: false
						} 
						];
						
				var a3Data = [
						{
							type: "label",
    						label: "Тарифная часть:"
						}, {
							type: "input",
							label: "Месячный оклад (руб.):",
							name: "oklad",
    						width: 80
						}, {
							type: "label",
    						label: "Премии:"
						}, {
							type: "input",
							label: "Премия за текущие результаты (%):",
							name: "proc_prem",
    						width: 40
						}, {
							type: "label",
    						label: "Надбавки:"
						}, {
							type: "input",
							label: "Персональная надбавка (руб.):",
							name: "nadbavka",
    						width: 80
						}, {
							type: "label",
    						label: "Доплаты:"
						}, {
							type: "input",
							label: "Доплата за длительное совмещение (руб.):",
							name: "dopl_sovm",
    						width: 80
						}, {
							type: "input",
							label: "Доплата за гостайну (%):",
							name: "proc_dopl_secret",
    						width: 40
						}, {
							type: "input",
							label: "Доплата за вредные условия труда (%):",
							name: "proc_dopl_vred",
    						width: 40
						}, {
							type: "input",
							label: "Доплата водителям за классность (%):",
							name: "proc_dopl_klass",
    						width: 40
						}, {
							type: "input",
							label: "Доплата за статус \"молодого специалиста\" (руб.):",
							name: "dopl_molod_spec",
    						width: 80
						}, {
							type: "input",
							label: "Районный коэффициент (%):",
							name: "proc_rk",
    						width: 40
						}, {
							type: "label",
    						label: "Способ отражения зарплаты в бухучете:"
						}, {
							type: "input",
							name: "id_otr_bux",
    						width: 20
						}, {
							type: "input",
							name: "otr_bux",
    						width: 500
						}, {
							type: "button",
							name: "sel_otr_bux",
							value: "Выбрать"
						}
						];
						
				var a4Data = [
						{
							type: "label",
    						label: "Сетевое имя пользователя:"
						}, {
							type: "input",
							name: "ip",
    						width: 200
						}, {
							type: "label",
    						label: "Профиль доступа:"
						}, {
    						type: "select",
							name: "profile",
    						width: 150,
							options: [
							{value: "0",text: "Без доступа", selected: true}, 
							{value: "1",text: "Работник", selected: true}, 
							{value: "2",text: "Начальник", selected: false},
							{value: "3",text: "Руководство", selected: false},
							{value: "4",text: "ОТиЗ", selected: false},
							{value: "5",text: "Администратор", selected: false}
									 ]
						}
						];
				
				var a5Data = [
						{
							type: "label",
    						label: "Трудовой договор:"
						}, {
							type: "checkbox",
							name: "osn_td",
    						label: "Новый трудовой договор",
							value: 1,
							checked: false
						}, {
							type: "input",
							name: "num_td",
							label: "№:",
							width: 50
						}, {
							type: "input",
							name: "date_td",
							label: "Дата заключения:",
							width: 70
						}, {
							type: "label",
							label: "Дополнительное соглашение:"
						}, {
							type: "checkbox",
							name: "osn_ds",
							label: "Новое дополнительное соглашение",
							value: 1,
							checked: false
						}, {
							type: "select", name: "vid_ds", label: "Вид соглашения:", width: 90,
							options:[
										{value: "", text: "---"},
										{value: "k", text: "Кадровое"},
										{value: "z", text: "Зарплатное"}
									]
						}, {
							type: "input",
							name: "num_ds",
							label: "№:",
							width: 50
						}, {
							type: "button",
							name: "get_num_ds",
							value: "Получить №"
						}, {
							type: "input",
							name: "date_ds",
							label: "Дата заключения:",
							width: 70
						}, {
							type: "label",
							label: "Причина изменения параметров:"
						}, {
							type: "select", name: "prich_izm", width: 250,
							options:[
										{value: "", text: "---"},
										{value: "1", text: "Прием сотрудника"},
										{value: "2", text: "Увольнение сотрудника"},
										{value: "3", text: "Перевод сотрудника"},
										{value: "4", text: "Изменение условий трудового договора",
											list: [	
													{
														type: "label",
														label: "Измененные разделы трудового договора:"
													}, {
														type: "checkbox",
														name: "usl_trud",
														label: "5. Характеристика условий труда",
														value: 1,
														checked: false
													}, {
														type: "input",
														name: "date_end_usl_trud",
														label: "Срок действия:",
														width: 70
													}, {
														type: "checkbox",
														name: "reg_trud_otd",
														label: "6. Рабочее время и время отдыха",
														value: 1,
														checked: false
													}, {
														type: "input",
														name: "date_end_reg_trud_otd",
														label: "Срок действия:",
														width: 70
													}, {
														type: "checkbox",
														name: "usl_opl",
														label: "7. Условия оплаты труда",
														value: 1,
														checked: false
													}, {
														type: "input",
														name: "date_end_usl_opl",
														label: "Срок действия:",
														width: 70
													}]},
										{value: "5", text: "Изменение персональных данных"},
									]
						}, {
    						type: "label",
    						label: "Статус уволенного:"
						}, {
    						type: "checkbox",
							name: "uvolen",
    						label: "Работает",
							value: 1,
							checked: false
						}, {
    						type: "label",
    						label: "Статус нахождения в декретном отпуске:"
						}, {
							type: "checkbox",
							name: "dekret",
    						label: "В декретном отпуске",
							value: 1,
							checked: false
						}
						];
				
				var a6Data = [
						{
							type: "label",
    						label: "Дата рождения:"
						}, {
							type: "input",
							name: "date_rogd",
    						width: 70
						}, {
							type: "label",
    						label: "Пол:"
						}, {
							type: "select", name: "pol", width: 80,
							options:[
										{value: "", text: "---"},
										{value: "m", text: "Мужской"},
										{value: "w", text: "Женский"}
									]
						}, {
							type: "label",
    						label: "Паспортные данные:"
						}, {
							type: "input",
							name: "ser_pasp",
							label: "Серия:",
    						width: 50
						}, {
							type: "input",
							name: "num_pasp",
							label: "Номер:",
    						width: 50
						}, {
							type: "input",
							name: "kem_vid_pasp",
							label: "Кем выдан:",
    						width: 450
						}, {
							type: "input",
							name: "date_vid_pasp",
							label: "Дата выдачи:",
    						width: 70
						}, {
							type: "input",
							name: "kod_podr_pasp",
							label: "Код подразделения:",
    						width: 70
						}, {
							type: "label",
    						label: "Адрес:"
						}, {
							type: "input",
							name: "adres_propis",
							label: "Адрес по прописке:",
    						width: 450
						}, {
							type: "label",
    						label: "Образование:"
						}, {
							type: "input",
							name: "date_end_obraz",
							label: "Дата окончания:",
    						width: 70
						}, {
							type: "input",
							name: "id_vid_obraz",
							label: "ID_vid_obraz:",
    						width: 50
						}, {
							type: "input",
							name: "vid_obraz",
							label: "Вид образования:",
    						width: 450
						}, {
							type: "button",
							name: "sel_vid_obraz",
							value: "Выбрать"
						}
						];
			
			dhxListNar1 = tabbarEditPers.cells("a1").attachForm(a1Data);
			dhxListNar2 = tabbarEditPers.cells("a2").attachForm(a2Data);
			dhxListNar3 = tabbarEditPers.cells("a3").attachForm(a3Data);
			dhxListNar4 = dhxLayoutEditPersTab_4.cells("a").attachForm(a4Data);
			dhxListNar5 = tabbarEditPers.cells("a5").attachForm(a5Data);
			dhxListNar6 = tabbarEditPers.cells("a6").attachForm(a6Data);
			
			dhxListNar1.attachEvent("onBeforeChange", function (id, old_value, new_value)
											{
												//alert(new_value.substr(0,1));
												if(id=="tabn" && (new_value.length!=5 || new_value.substr(0,1)!="0")) {alert("Неверное значение!"); return false;}
												 return true;
											});
			
			dhxListNar1.attachEvent("onButtonClick", function(name, command)
											{
												if(name=="sel_podrazd") WinSpr_podrazd("spr_podrazd","dhxListNar1",dhxListNar1.getItemValue("id_podrazd"));
												
												if(name=="sel_profes") WinSpr_prof("spr_prof","dhxListNar1",dhxListNar1.getItemValue("id_profes"));
												
												if(name=="sel_kateg") WinSpr_kateg_pers("spr_kateg_pers","dhxListNar1",dhxListNar1.getItemValue("id_kateg"));
											});
			
			dhxListNar2.attachEvent("onButtonClick", function(name, command)
											{
												if(name=="sel_propusk") WinSpr_vid_prop("spr_vid_prop",1,dhxListNar2.getItemValue("id_prop"));
												
												if(name=="sel_graf") WinSpr_graf("spr_graf",1,dhxListNar2.getItemValue("id_graf"));
												
												if(name=="sel_date_end_graf") {var winPos = dhxWinsEditPers.getPosition(); mCal4.setPosition(winPos[1]+380, winPos[0]+60);  mCal4.show(); document.getElementById("modal_background").style.display = "block";}
											});
											
			dhxListNar2.attachEvent("onBeforeChange", function (id, old_value, new_value)
											{
												if(id=="nenorm")
												{
													if(dhxListNar2.isItemChecked("nenorm"))//Если снимаем галку
													{
														dhxListNar2.setItemLabel("nenorm", "Не установлен");
													}
													else
													{
														dhxListNar2.setItemLabel("nenorm", "Установлен");
													}
												}
												return true;
											});								
											
			dhxListNar3.attachEvent("onButtonClick", function(name, command)
											{
												if(name=="sel_otr_bux") WinSpr_otr_bux("spr_otr_bux","nar",dhxListNar3.getItemValue("id_otr_bux"));
											});
											
			dhxListNar5.attachEvent("onBeforeChange", function (id, old_value, new_value)
											{
												
												if(id=="vid_ds")
												{
													//alert(new_value);
													if(new_value=="k")
													{
														dhxListNar5._enableItem("num_ds");
														dhxListNar5.setItemValue("num_ds","");
														dhxListNar5._disableItem("get_num_ds");
														dhxListNar5._enableItem("date_ds");
													}
													else if(new_value=="z")
													{
														dhxListNar5._disableItem("num_ds");
														dhxListNar5.setItemValue("num_ds","");
														dhxListNar5._enableItem("get_num_ds");
														dhxListNar5._enableItem("date_ds");
													}
													else
													{
														dhxListNar5._disableItem("num_ds");
														dhxListNar5._disableItem("get_num_ds");
														dhxListNar5._disableItem("date_ds");
													}
												}
												
												
												if(id=="osn_ds")
												{
													if(dhxListNar5.isItemChecked("osn_ds"))//Если снимаем галку
													{
														
														dhxListNar5.setItemValue("num_ds","");
														dhxListNar5.setItemValue("date_ds","");
														var opts_vid_ds = dhxListNar5.getOptions("vid_ds");
														for (var q = 0; q < opts_vid_ds.length; q++)
														{
															if(opts_vid_ds[q].value =="") opts_vid_ds[q].selected = true;
														}
														
														dhxListNar5._disableItem("get_num_ds");
														dhxListNar5._disableItem("vid_ds");
														dhxListNar5._disableItem("num_ds");
														dhxListNar5._disableItem("date_ds");
													}
													else//Если ставим галку
													{
														dhxListNar5._enableItem("vid_ds");
														dhxListNar5._enableItem("date_ds");
														dhxListNar5.setItemValue("num_ds","");
														dhxListNar5.setItemValue("date_ds","");
													}
												}
												
												if(id=="osn_td")
												{
													if(dhxListNar5.isItemChecked("osn_td"))//Если снимаем галку
													{
														dhtmlxAjax.get("windows/nar/getInfo.php?action=get_last_td&id="+dhxListNar1.getItemValue("id"),
														function(loader)
														{
															//alert(loader.xmlDoc.responseText);
															var ArrayParamTD =Array();
															ArrayParamTD =loader.xmlDoc.responseText.split('|');
															dhxListNar5.setItemValue("num_td",ArrayParamTD[1]);
															dhxListNar5.setItemValue("date_td",ArrayParamTD[2]);
														});
														
														dhxListNar5._disableItem("num_td");
														dhxListNar5._disableItem("date_td");
													}
													else//Если ставим галку
													{
														dhxListNar5._enableItem("num_td");
														dhxListNar5._enableItem("date_td");
														dhxListNar5.setItemValue("num_td","");
														dhxListNar5.setItemValue("date_td","");
													}
												}
												
												if(id=="prich_izm")
												{
													if(new_value=="2")
													{
														dhxListNar5.checkItem("uvolen");
														dhxListNar5.setItemLabel("uvolen", "Установлен");
													}
													else
													{
														dhxListNar5.uncheckItem("uvolen");
														dhxListNar5.setItemLabel("uvolen", "Не установлен");
														
														if(new_value=="4")//если выбираем условия трудового договора, то восстанавливаем исходные данные
														{
															if(ArrayParam[54]=="1")	{dhxListNar5.checkItem("usl_trud"); dhxListNar5.showItem("date_end_usl_trud"); dhxListNar5.setItemValue("date_end_usl_trud",ArrayParam[57]);}				else	{dhxListNar5.uncheckItem("usl_trud"); dhxListNar5.hideItem("date_end_usl_trud");}
															if(ArrayParam[55]=="1")	{dhxListNar5.checkItem("reg_trud_otd"); dhxListNar5.showItem("date_end_reg_trud_otd"); dhxListNar5.setItemValue("date_end_reg_trud_otd",ArrayParam[58]);}	else	{dhxListNar5.uncheckItem("reg_trud_otd"); dhxListNar5.hideItem("date_end_reg_trud_otd");}
															if(ArrayParam[56]=="1")	{dhxListNar5.checkItem("usl_opl"); dhxListNar5.showItem("date_end_usl_opl"); dhxListNar5.setItemValue("date_end_usl_opl",ArrayParam[59]);}					else	{dhxListNar5.uncheckItem("usl_opl"); dhxListNar5.hideItem("date_end_usl_opl");}
														}
														else//иначе снимаем галки и очищаем поля даты
														{
															dhxListNar5.uncheckItem("usl_trud");
															dhxListNar5.uncheckItem("reg_trud_otd");
															dhxListNar5.uncheckItem("usl_opl");
															
															dhxListNar5.setItemValue("date_end_usl_trud","");
															dhxListNar5.setItemValue("date_end_reg_trud_otd","");
															dhxListNar5.setItemValue("date_end_usl_opl","");
														}
													}
												}
												
												
												if(id=="usl_trud")
												{
													if(dhxListNar5.isItemChecked("usl_trud"))//Если снимаем галку
													{
														dhxListNar5.hideItem("date_end_usl_trud");
														dhxListNar5.setItemValue("date_end_usl_trud","");
													}
													else
													{
														dhxListNar5.showItem("date_end_usl_trud");
														dhxListNar5.setItemValue("date_end_usl_trud",ArrayParam[57]);
													}
												}
												
												if(id=="reg_trud_otd")
												{
													if(dhxListNar5.isItemChecked("reg_trud_otd"))//Если снимаем галку
													{
														dhxListNar5.hideItem("date_end_reg_trud_otd");
														dhxListNar5.setItemValue("date_end_reg_trud_otd","");
													}
													else
													{
														dhxListNar5.showItem("date_end_reg_trud_otd");
														dhxListNar5.setItemValue("date_end_reg_trud_otd",ArrayParam[58]);
													}
												}
												
												if(id=="usl_opl")
												{
													if(dhxListNar5.isItemChecked("usl_opl"))//Если снимаем галку
													{
														dhxListNar5.hideItem("date_end_usl_opl");
														dhxListNar5.setItemValue("date_end_usl_opl","");
													}
													else
													{
														dhxListNar5.showItem("date_end_usl_opl");
														dhxListNar5.setItemValue("date_end_usl_opl",ArrayParam[59]);
													}
												}
												
												
												if(id=="uvolen")
												{
													if(dhxListNar5.isItemChecked("uvolen"))//Если снимаем галку
													{
														dhxListNar5.setItemLabel("uvolen", "Не установлен");
													}
													else
													{
														dhxListNar5.setItemLabel("uvolen", "Установлен");
													}
												}
												
												if(id=="dekret")
												{
													if(dhxListNar5.isItemChecked("dekret"))//Если снимаем галку
													{
														dhxListNar5.setItemLabel("dekret", "Не установлен");
													}
													else
													{
														dhxListNar5.setItemLabel("dekret", "Установлен");
													}
												}
												
												return true;
											});
			
			dhxListNar5.attachEvent("onButtonClick", function(name, command)
											{
												if(name=="get_num_ds")
												{
													dhtmlxAjax.get("windows/nar/getInfo.php?action=get_num_ds_z&tabn="+dhxListNar1.getItemValue("tabn")+"&num_td="+dhxListNar5.getItemValue("num_td")+"&date_beg="+toolbarWinEditPers.getValue("date_beg"),
														function(loader)
														{
															//alert(loader.xmlDoc.responseText);
															var ArrayNewNumDS =Array();
															ArrayNewNumDS =loader.xmlDoc.responseText.split('|');
															dhxListNar5.setItemValue("num_ds",ArrayNewNumDS[1]);
															dhxListNar5.setItemValue("date_ds",toolbarWinEditPers.getValue("date_beg"));
														});
												}
											});
			
			dhxListNar6.attachEvent("onButtonClick", function(name, command)
											{
												if(name=="sel_vid_obraz") WinSpr_vid_obraz("spr_vid_obraz","nar",dhxListNar6.getItemValue("id_vid_obraz"));
											});
			
			dhxListNar1.setSkin(form_skin);
			dhxListNar2.setSkin(form_skin);
			dhxListNar3.setSkin(form_skin);
			
			
			
			if(idBut=="new")
						{
							dhxWinsEditPers.setText("Добавление нового сотрудника");
							dhxListNar1.setItemValue("id_podrazd",dhxNarTree.getSelectedItemId());
							dhtmlxAjax.get("windows/nar/getInfo.php?action=get_podrazd&id="+dhxNarTree.getSelectedItemId(),
												function(loader)
													{
														ArrayParam =loader.xmlDoc.responseText.split('|');
														dhxListNar1.setItemValue("podrazd",ArrayParam[2]);
													});
							
							
							var cure_date = new Date();
							mCal_pers_beg.setFormatedDate("%e.%c.%Y",cure_date.getDate()+"."+(cure_date.getMonth()+1)+"."+cure_date.getFullYear());
							toolbarWinEditPers.setValue("date_beg", mCal_pers_beg.getFormatedDate("%d.%m.%Y"));
							
							mCal_pers_end.setFormatedDate("%d.%m.%Y","01.01.2100");
							toolbarWinEditPers.setValue("date_end", mCal_pers_end.getFormatedDate("%d.%m.%Y"));
							
							//Активируем поле для ввода табельного номера
							dhxListNar1._enableItem("tabn");
							dhxListNar5._disableItem("osn_ds");
							var opts_prich_izm = dhxListNar5.getOptions("prich_izm");
							for (var q = 0; q < opts_prich_izm.length; q++)
							{
								if(opts_prich_izm[q].value == "1") opts_prich_izm[q].selected = true;
							}
						}
						
			if(idBut=="edit" || idBut=="add_copy")
						{
							var win_text;
							dhtmlxAjax.get("windows/nar/getInfo.php?action=person&id="+id+"&date="+winNar_toolbar.getValue("input_date_beg"),function(loader)
							{
								//alert(loader.xmlDoc.responseText);
								ArrayParam =loader.xmlDoc.responseText.split('|');
								
								if(idBut=="edit") win_text = " <font color=blue>[редактирование]</font>";
								if(idBut=="add_copy") win_text = " <font color=blue>[добавление копированием]</font>";
								
								dhxWinsEditPers.setText(ArrayParam[4]+" "+ArrayParam[5]+" "+ArrayParam[6]+" ("+ArrayParam[1]+")"+win_text);

								mCal_pers_beg.setFormatedDate("%d.%m.%Y",ArrayParam[2]);
								toolbarWinEditPers.setValue("date_beg", mCal_pers_beg.getFormatedDate("%d.%m.%Y"));
								
								mCal_pers_end.setFormatedDate("%d.%m.%Y",ArrayParam[3]);
								toolbarWinEditPers.setValue("date_end", mCal_pers_end.getFormatedDate("%d.%m.%Y"));
								
								dhxListNar1._disableItem("tabn");
								dhxListNar1.setItemValue("tabn",ArrayParam[1]);

								dhxListNar1.setItemValue("fam",ArrayParam[4]);
								dhxListNar1.setItemValue("fam_rod",ArrayParam[26]);
								dhxListNar1.setItemValue("name",ArrayParam[5]);
								dhxListNar1.setItemValue("otch",ArrayParam[6]);
								
								dhxListNar1.setItemValue("id_podrazd",ArrayParam[7]);
								dhxListNar1.setItemValue("podrazd",ArrayParam[8]);
								dhxListNar1.setItemValue("id_profes",ArrayParam[9]);
								dhxListNar1.setItemValue("profes",ArrayParam[10]);
								dhxListNar1.setItemValue("id_kateg",ArrayParam[11]);
								dhxListNar1.setItemValue("kateg",ArrayParam[12]);
								dhxListNar2.setItemValue("id_prop",ArrayParam[13]);
								dhxListNar2.setItemValue("propusk",ArrayParam[14]);
								dhxListNar2.setItemValue("id_graf",ArrayParam[15]);
								dhxListNar2.setItemValue("graf",ArrayParam[16]);
								dhxListNar2.setItemValue("stavka",ArrayParam[53]);
								
								mCal4.setFormatedDate("%d.%m.%Y",ArrayParam[17]);
								dhxListNar2.setItemValue("date_end_graf",mCal4.getFormatedDate("%d.%m.%Y"));
								
								if(ArrayParam[18]==1)
								{
									dhxListNar2.checkItem("nenorm");
									dhxListNar2.setItemLabel("nenorm", "Установлен");
								}
								else
								{
									dhxListNar2.uncheckItem("nenorm");
									dhxListNar2.setItemLabel("nenorm", "Не установлен");
								}
								dhxListNar3.setItemValue("oklad",ArrayParam[19]);
								dhxListNar3.setItemValue("nadbavka",ArrayParam[20]);
								dhxListNar3.setItemValue("proc_prem",ArrayParam[21]);
								dhxListNar3.setItemValue("dopl_sovm",ArrayParam[27]);
								dhxListNar3.setItemValue("proc_dopl_secret",ArrayParam[28]);
								dhxListNar3.setItemValue("proc_dopl_vred",ArrayParam[29]);
								dhxListNar3.setItemValue("proc_dopl_klass",ArrayParam[30]);
								dhxListNar3.setItemValue("dopl_molod_spec",ArrayParam[31]);
								dhxListNar3.setItemValue("proc_rk",ArrayParam[60]);
								
								dhxListNar3.setItemValue("id_otr_bux",ArrayParam[32]);
								dhxListNar3.setItemValue("otr_bux",ArrayParam[33]);
								
								dhxListNar5.setItemValue("num_td",ArrayParam[34]);
								dhxListNar5.setItemValue("date_td",ArrayParam[35]);
								
								if(ArrayParam[50]==1)
								{
									dhxListNar5.checkItem("osn_td");
									dhxListNar5._enableItem("num_td");
									dhxListNar5._enableItem("date_td");
								}
								else
								{
									dhxListNar5.uncheckItem("osn_td");
									dhxListNar5._disableItem("num_td");
									dhxListNar5._disableItem("date_td");
								}
								
								
								if(idBut=="edit")
								{
									
									dhxListNar5.setItemValue("num_ds",ArrayParam[37]);
									dhxListNar5.setItemValue("date_ds",ArrayParam[38]);
									
									if(ArrayParam[51]==1)
									{
										dhxListNar5.checkItem("osn_ds");
										dhxListNar5._enableItem("vid_ds");
										dhxListNar5._enableItem("date_ds");
										if(ArrayParam[36]=="z")
										{
											//dhxListNar5._enableItem("num_ds");
											dhxListNar5._disableItem("num_ds");
											dhxListNar5._enableItem("get_num_ds");
										}
										else
										{
											//alert(ArrayParam[53]+"_"+dhxListNar5.getItemValue("vid_ds"));
											if(ArrayParam[36]=="k")dhxListNar5._enableItem("num_ds");
											dhxListNar5._disableItem("get_num_ds");
										}
									}
									else
									{
										//alert(ArrayParam[53]);
										dhxListNar5.uncheckItem("osn_ds");
										dhxListNar5._disableItem("vid_ds");
										dhxListNar5._disableItem("num_ds");
										dhxListNar5._disableItem("get_num_ds");
										dhxListNar5._disableItem("date_ds");
									}
									
									var opts_vid_ds = dhxListNar5.getOptions("vid_ds");
									for (var q = 0; q < opts_vid_ds.length; q++)
									{
										if(opts_vid_ds[q].value == ArrayParam[36]) opts_vid_ds[q].selected = true;
									}
									
									var opts_prich_izm = dhxListNar5.getOptions("prich_izm");
									for (var q = 0; q < opts_prich_izm.length; q++)
									{
										if(opts_prich_izm[q].value == ArrayParam[52]) opts_prich_izm[q].selected = true;
									}
								}
								
								if(ArrayParam[54]=="1")	{dhxListNar5.checkItem("usl_trud"); dhxListNar5.showItem("date_end_usl_trud"); dhxListNar5.setItemValue("date_end_usl_trud",ArrayParam[57]);}				else	{dhxListNar5.uncheckItem("usl_trud"); dhxListNar5.hideItem("date_end_usl_trud");}
								if(ArrayParam[55]=="1")	{dhxListNar5.checkItem("reg_trud_otd"); dhxListNar5.showItem("date_end_reg_trud_otd"); dhxListNar5.setItemValue("date_end_reg_trud_otd",ArrayParam[58]);}	else	{dhxListNar5.uncheckItem("reg_trud_otd"); dhxListNar5.hideItem("date_end_reg_trud_otd");}
								if(ArrayParam[56]=="1")	{dhxListNar5.checkItem("usl_opl"); dhxListNar5.showItem("date_end_usl_opl"); dhxListNar5.setItemValue("date_end_usl_opl",ArrayParam[59]);}					else	{dhxListNar5.uncheckItem("usl_opl"); dhxListNar5.hideItem("date_end_usl_opl");}
								
								dhxListNar6.setItemValue("date_rogd",ArrayParam[39]);
								dhxListNar6.setItemValue("ser_pasp",ArrayParam[41]);
								dhxListNar6.setItemValue("num_pasp",ArrayParam[42]);
								dhxListNar6.setItemValue("kem_vid_pasp",ArrayParam[43]);
								dhxListNar6.setItemValue("date_vid_pasp",ArrayParam[44]);
								dhxListNar6.setItemValue("kod_podr_pasp",ArrayParam[45]);
								dhxListNar6.setItemValue("adres_propis",ArrayParam[46]);
								dhxListNar6.setItemValue("id_vid_obraz",ArrayParam[47]);
								dhxListNar6.setItemValue("vid_obraz",ArrayParam[48]);
								dhxListNar6.setItemValue("date_end_obraz",ArrayParam[49]);
								
								
								var opts_pol = dhxListNar6.getOptions("pol");
								for (var q = 0; q < opts_pol.length; q++)
								{
									if(opts_pol[q].value == ArrayParam[40]) opts_pol[q].selected = true;
								}
								
								
								if(ArrayParam[22]==1)
								{
									dhxListNar5.checkItem("uvolen");
									dhxListNar5.setItemLabel("uvolen", "Установлен");
								}
								else
								{
									dhxListNar5.uncheckItem("uvolen");
									dhxListNar5.setItemLabel("uvolen", "Не установлен");
								}
								
								if(ArrayParam[23]==1)
								{
									dhxListNar5.checkItem("dekret");
									dhxListNar5.setItemLabel("dekret", "Установлен");
								}
								else
								{
									dhxListNar5.uncheckItem("dekret");
									dhxListNar5.setItemLabel("dekret", "Не установлен");
								}
								
								
								dhxListNar4.setItemValue("ip",ArrayParam[24]);
								
								var opts_profiles = dhxListNar4.getOptions("profile");
								opts_profiles[ArrayParam[25]].selected = true;
								
								if(ArrayParam[25]=="2" || ArrayParam[25]=="3")
								{
									toolbarWinEditPersTab_4.enableItem("add_podr");
									toolbarWinEditPersTab_4.enableItem("del_podr");
								}
								else
								{
									toolbarWinEditPersTab_4.disableItem("add_podr");
									toolbarWinEditPersTab_4.disableItem("del_podr");
								}
								
							});
							
						}

			dhxListNar1.hideItem("action");
			dhxListNar1.hideItem("id");
			dhxListNar1.hideItem("chTabn");
			dhxListNar1.hideItem("id_podrazd");
			dhxListNar1._disableItem("podrazd");
			dhxListNar1.hideItem("id_profes");
			dhxListNar1._disableItem("profes");
			dhxListNar1.hideItem("id_kateg");
			dhxListNar1._disableItem("kateg");
			
			dhxListNar2.hideItem("id_graf");
			dhxListNar2._disableItem("graf");
			dhxListNar2.hideItem("id_prop");
			dhxListNar2._disableItem("propusk");
			dhxListNar2._disableItem("stavka");
			dhxListNar2.hideItem("date_end_graf");
			dhxListNar2.hideItem("sel_date_end_graf");
			dhxListNar3.hideItem("id_otr_bux");
			
			dhxListNar3._disableItem("otr_bux");
			
			dhxListNar4._disableItem("ip");
			
			dhxListNar5._disableItem("uvolen");
			dhxListNar5._disableItem("num_td");
			dhxListNar5._disableItem("date_td");
			dhxListNar5._disableItem("vid_ds");
			dhxListNar5._disableItem("num_ds");
			dhxListNar5._disableItem("get_num_ds");
			dhxListNar5._disableItem("date_ds");
			dhxListNar5.hideItem("");
			
			dhxListNar6.hideItem("id_vid_obraz");
			dhxListNar6._disableItem("vid_obraz");
			
			
			if(dhxListNar4.getItemValue("profile")=="2"){dhxListNar4.showItem("podr_drive_1"); dhxListNar4.showItem("sel_podr_drive_1");}
			
			dhxListNar4.attachEvent("onBeforeChange", function (id_Item, old_value, new_value)
											{
												if(id_Item=="profile" && new_value!="2" && new_value!="3")
												{
													//Очищаем таблицу подразделений и деактивируем кнопки
													dhxGridEditPersTab_4.clearAll();
													toolbarWinEditPersTab_4.disableItem("add_podr");
													toolbarWinEditPersTab_4.disableItem("del_podr");
												}
												else if(id_Item=="profile")
												{
													dhxGridEditPersTab_4.clearAndLoad("windows/nar/getGridPodr.php?id="+id);
													toolbarWinEditPersTab_4.enableItem("add_podr");
													toolbarWinEditPersTab_4.enableItem("del_podr");
												}
												else if(id_Item=="ip")
												{
													var domen = "@SKBM.RU";
													if(new_value!="" && new_value.indexOf(domen)==-1)
													{
														dhxListNar4.setItemValue("ip",new_value+domen);
													}
												}
												return true;
											});
			
			dhxListNar1.attachEvent("onChange",function (idch,value,checked)
											{
												if(idch=="fam") dhxListNar1.setItemValue("fam",value.toUpperCase());//переводим в верхний регистр
												if(idch=="fam_rod") dhxListNar1.setItemValue("fam_rod",value.toUpperCase());//переводим в верхний регистр
												if(idch=="name") dhxListNar1.setItemValue("name",value.toUpperCase());//переводим в верхний регистр
												if(idch=="otch") dhxListNar1.setItemValue("otch",value.toUpperCase());//переводим в верхний регистр
											});

		}
		
		
		
		function WinEditListPers(idWin)
		{
			//СОЗДАЕМ ОКНО
			var dhxWinsEditListPers = dhxWins.createWindow(idWin, 1, 1, 800, 500);//ПОЗИЦИЯ И РАЗМЕРЫ ОКНА
			dhxWinsEditListPers.setText("Массовое редактирование параметров оплаты работников по записям, действующим на выбранную дату");
			dhxWinsEditListPers.setIcon("group.png","");
			dhxWinsEditListPers.maximize(true);
			
			var sb = dhxWinsEditListPers.attachStatusBar();
			sb.setText("<font color='red'>Красным цветом выделены записи, после которых существует история изменений. Массовое редактирование по таким записям невозможно.</font>");
			
			//ВСТАВЛЯЕМ ТУЛБАР
			editListPersToolbar = dhxWinsEditListPers.attachToolbar();													
			editListPersToolbar.setIconsPath("dhtmlxSuite/dhtmlxToolbar/samples/common/imgs/");
			editListPersToolbar.setSkin(toolbar_skin);

			editListPersToolbar.addText("text_1",0, "Текущая дата: ");
			editListPersToolbar.addInput("input_date",1,"",65);
			editListPersToolbar.setValue("input_date",getCurDate());
			editListPersToolbar.disableItem("input_date");
			editListPersToolbar.addButton("but_date_list_edit_pers",2,"","calendar.gif","");
			editListPersToolbar.addSeparator("sep1",3);
			
			editListPersToolbar.addButton("but_copy",4,"Создать записи","copy.gif","copy_dis.gif");
			editListPersToolbar.setItemToolTip("but_copy","Создать новые записи копированием с текущей даты");
			editListPersToolbar.addSeparator("sep2",5);
			
			editListPersToolbar.addButton("but_del",6,"Удалить записи","delete.gif","delete_dis.gif");
			editListPersToolbar.setItemToolTip("but_del","Удалить выбранные записи");
			editListPersToolbar.addSeparator("sep3",7);
			
			editListPersToolbar.addText("text_2",8,"Коэффициент: ");
			editListPersToolbar.addInput("input_index",9,"1",40);
			editListPersToolbar.setItemToolTip("input_index","Коэффициент увеличения текущего значения");
			editListPersToolbar.addText("text_3",10,"Округлять");
			
			editListPersToolbar.addButtonTwoState("round_up",11,"вверх","","");
			editListPersToolbar.setItemState("round_up",true);
			editListPersToolbar.setItemToolTip("round_up","Направление округления");
			editListPersToolbar.addText("text_4",12,"до: ");
			
			editListPersToolbar.addInput("input_round",13,"1",40);
			editListPersToolbar.setItemToolTip("input_round","Степень округления (1,10,100,1000...)");
			
			editListPersToolbar.addButton("but_set_index",14,"Расчитать","ar_right.gif","ar_right_dis.gif");
			editListPersToolbar.setItemToolTip("but_set_index","Расчитать новые значения по коэффициенту");
			editListPersToolbar.addSeparator("sep4",15);
			
			editListPersToolbar.addText("text_5",16,"Значение: ");
			editListPersToolbar.addInput("input_value",17,"",60);
			editListPersToolbar.setItemToolTip("input_value","Новое значение");
			editListPersToolbar.addButton("but_set_value",18,"Установить","ar_right.gif","ar_right_dis.gif");
			editListPersToolbar.setItemToolTip("but_set_value","Установить указанное новое значение");
			editListPersToolbar.addSeparator("sep5",19);
			editListPersToolbar.addButton("save",20,"Сохранить изменения","save.gif","");
			editListPersToolbar.addSeparator("sep6",21);
			
			editListPersToolbar.attachEvent("onStateChange", function(idState, state)
														{
															if(idState=="round_up")
															{
																if(state==true)editListPersToolbar.setItemText("round_up","вверх");
																if(state==false)editListPersToolbar.setItemText("round_up","вниз");
															}
														});
														
			editListPersToolbar.attachEvent("onClick", function(idBut)
													{
														if(idBut=="but_copy" && confirm("Создать новые записи копированием с "+editListPersToolbar.getValue("input_date")+"г.?"))
														{
															if(dhxGridEditListPers.getCheckedRows(0)!="")
															{
																dhtmlxAjax.get("windows/nar/savePers.php?action=copyListPers&list_id="+dhxGridEditListPers.getCheckedRows(0)+"&date_beg="+editListPersToolbar.getValue("input_date"),
																function(loader)
																{
																	//alert(loader.xmlDoc.responseText);
																	dhxGridEditListPers.clearAndLoad("windows/nar/getGrid_list_edit.php?date="+editListPersToolbar.getValue("input_date"));
																});
															}
															else
															{
																alert("Выберите записи для копирования!");
															}
														}
														
														if(idBut=="but_del" && confirm("Удалить выбранные записи?"))
														{
															if(dhxGridEditListPers.getCheckedRows(0)!="")
															{
																dhtmlxAjax.get("windows/nar/savePers.php?action=delListPers&list_id="+dhxGridEditListPers.getCheckedRows(0),
																function(loader)
																{
																	//alert(loader.xmlDoc.responseText);
																	dhxGridEditListPers.clearAndLoad("windows/nar/getGrid_list_edit.php?date="+editListPersToolbar.getValue("input_date"));
																});
															}
															else
															{
																alert("Выберите записи для удаления!");
															}
														}
														
														if(idBut=="but_date_list_edit_pers")
														{
															document.getElementById("modal_background").style.display = "block";
															Calendar(idBut,window.event.clientX, window.event.clientY, editListPersToolbar.getValue("input_date"));
														}
														
														if(idBut=="but_set_index")
														{
															if(dhxGridEditListPers.getCheckedRows(0)!="")
															{
																if(document.getElementById("ch_opl_1").checked || document.getElementById("ch_opl_2").checked || document.getElementById("ch_opl_3").checked || document.getElementById("ch_opl_4").checked || document.getElementById("ch_opl_5").checked || document.getElementById("ch_opl_6").checked || document.getElementById("ch_opl_7").checked || document.getElementById("ch_opl_8").checked || document.getElementById("ch_opl_9").checked)
																{
																	if(editListPersToolbar.getValue("input_index")!="" && editListPersToolbar.getValue("input_round")!="")
																	{
																		var array_rows_id = Array();
																		array_rows_id = dhxGridEditListPers.getCheckedRows(0).split(',');
																		
																		var array_vid_opl = new Array();
																		var i=1;
																		
																		if(document.getElementById("ch_opl_1").checked) array_vid_opl[i++] = 10;
																		if(document.getElementById("ch_opl_2").checked) array_vid_opl[i++] = 12;
																		if(document.getElementById("ch_opl_3").checked) array_vid_opl[i++] = 14;
																		if(document.getElementById("ch_opl_4").checked) array_vid_opl[i++] = 16;
																		if(document.getElementById("ch_opl_5").checked) array_vid_opl[i++] = 18;
																		if(document.getElementById("ch_opl_6").checked) array_vid_opl[i++] = 20;
																		if(document.getElementById("ch_opl_7").checked) array_vid_opl[i++] = 22;
																		if(document.getElementById("ch_opl_8").checked) array_vid_opl[i++] = 24;
																		if(document.getElementById("ch_opl_9").checked) array_vid_opl[i++] = 26;
																		
																		set_index(array_rows_id,array_vid_opl);
																	}
																	else
																	{
																		alert("Установите все параметры для корректировки!");
																	}
																}
																else
																{
																	alert("Выберите вид оплаты для корректировки!");
																}
															}
															else
															{
																alert("Выберите записи для корректировки!");
															}
														}
														
														if(idBut=="but_set_value")
														{
															if(dhxGridEditListPers.getCheckedRows(0)!="")
															{
																if(document.getElementById("ch_opl_1").checked || document.getElementById("ch_opl_2").checked || document.getElementById("ch_opl_3").checked || document.getElementById("ch_opl_4").checked || document.getElementById("ch_opl_5").checked || document.getElementById("ch_opl_6").checked || document.getElementById("ch_opl_7").checked || document.getElementById("ch_opl_8").checked || document.getElementById("ch_opl_9").checked)
																{
																	if(editListPersToolbar.getValue("input_value")!="")
																	{
																		var array_rows_id = Array();
																		array_rows_id = dhxGridEditListPers.getCheckedRows(0).split(',');
																		
																		var array_vid_opl = new Array();
																		var i=1;
																		
																		if(document.getElementById("ch_opl_1").checked) array_vid_opl[i++] = 10;
																		if(document.getElementById("ch_opl_2").checked) array_vid_opl[i++] = 12;
																		if(document.getElementById("ch_opl_3").checked) array_vid_opl[i++] = 14;
																		if(document.getElementById("ch_opl_4").checked) array_vid_opl[i++] = 16;
																		if(document.getElementById("ch_opl_5").checked) array_vid_opl[i++] = 18;
																		if(document.getElementById("ch_opl_6").checked) array_vid_opl[i++] = 20;
																		if(document.getElementById("ch_opl_7").checked) array_vid_opl[i++] = 22;
																		if(document.getElementById("ch_opl_8").checked) array_vid_opl[i++] = 24;
																		if(document.getElementById("ch_opl_9").checked) array_vid_opl[i++] = 26;
																		
																		set_value(array_rows_id,array_vid_opl);
																	}
																	else
																	{
																		alert("Установите новое значение для корректировки!");
																	}
																}
																else
																{
																	alert("Выберите вид оплаты для корректировки!");
																}
															}
															else
															{
																alert("Выберите записи для корректировки!");
															}
														}
														
														if(idBut=="save" && confirm("Сохранить изменения по выбранным записям?"))
														{
															if(dhxGridEditListPers.getCheckedRows(0)!="")
															{
																var arr_rowId = Array();
																arr_rowId = dhxGridEditListPers.getCheckedRows(0).split(',');
																
																var arr_colInd = new Array();
																arr_colInd[1]	= 8;
																arr_colInd[2]	= 10;
																arr_colInd[3]	= 12;
																arr_colInd[4]	= 14;
																arr_colInd[5]	= 16;
																arr_colInd[6]	= 18;
																arr_colInd[7]	= 20;
																arr_colInd[8]	= 22;
																arr_colInd[9]	= 24;
																arr_colInd[10]	= 26;
																
																var list_new_values="";
																
																for(i=1; i<arr_colInd.length; i++)
																{
																	for(j=0; j<arr_rowId.length; j++)
																	{
																		var old_value = dhxGridEditListPers.cellById(arr_rowId[j],arr_colInd[i]-1).getValue();
																		var new_value = dhxGridEditListPers.cellById(arr_rowId[j],arr_colInd[i]).getValue();
																		if(old_value!=new_value)//Если параметры изменены, то сохраняем новое значение
																		{
																			list_new_values = list_new_values + "|" + arr_rowId[j] + "," + arr_colInd[i] + "," + new_value;
																		}
																	}
																}
																
																if(list_new_values=="")
																{
																	alert("Нет измененных параметров!");
																}
																else
																{
																	//Сохраняем
																	dhtmlxAjax.get("windows/nar/savePers.php?action=saveListPers&list_new_values="+list_new_values,
																	function(loader)
																	{
																		//alert(loader.xmlDoc.responseText);
																		dhxGridEditListPers.clearAndLoad("windows/nar/getGrid_list_edit.php?date="+editListPersToolbar.getValue("input_date"));
																	});
																	
																	//alert(list_new_values);
																}
															}
															else
															{
																alert("Выберите записи для сохранения!");
															}
														}
														
													});
			
			function set_index(arr_rowId,arr_colInd)
			{
				for(i=1; i<arr_colInd.length; i++)
				{
					for(j=0; j<arr_rowId.length; j++)
					{
						var old_value = dhxGridEditListPers.cellById(arr_rowId[j],arr_colInd[i]-1).getValue();
						var index = editListPersToolbar.getValue("input_index").replace(",",".");
						if(editListPersToolbar.getItemState("round_up"))
						{
							var new_value = Math.ceil((old_value * index) / editListPersToolbar.getValue("input_round")) * editListPersToolbar.getValue("input_round");
						}
						else
						{
							var new_value = Math.floor((old_value * index) / editListPersToolbar.getValue("input_round")) * editListPersToolbar.getValue("input_round");
						}
						if(new_value==0)new_value="";
						
						
						dhxGridEditListPers.cellById(arr_rowId[j],arr_colInd[i]).setValue(new_value);
						
						//Меняем цвет по измененным параметрам
						if(old_value!=new_value)dhxGridEditListPers.setCellTextStyle(arr_rowId[j],arr_colInd[i],"font-family:Tahoma; font-size:11px; color:blue;");
						else dhxGridEditListPers.setCellTextStyle(arr_rowId[j],arr_colInd[i],"font-family:Tahoma; font-size:11px; color:black;");

					}
				}	
			}
			
			function set_value(arr_rowId,arr_colInd)
			{
				var new_value = editListPersToolbar.getValue("input_value");
				
				for(i=1; i<arr_colInd.length; i++)
				{
					for(j=0; j<arr_rowId.length; j++)
					{
						var old_value = dhxGridEditListPers.cellById(arr_rowId[j],arr_colInd[i]-1).getValue();
						
						if(new_value==0)new_value="";

						dhxGridEditListPers.cellById(arr_rowId[j],arr_colInd[i]).setValue(new_value);
						
						//Меняем цвет по измененным параметрам
						if(old_value!=new_value)dhxGridEditListPers.setCellTextStyle(arr_rowId[j],arr_colInd[i],"font-family:Tahoma; font-size:11px; color:blue;");
						else dhxGridEditListPers.setCellTextStyle(arr_rowId[j],arr_colInd[i],"font-family:Tahoma; font-size:11px; color:black;");

					}
				}	
			}
			
			
			dhxGridEditListPers = dhxWinsEditListPers.attachGrid();
			//alert(document.getElementById(\"ch_opl_2\").checked);
			dhxGridEditListPers.setHeader(",Таб.,ФИО,Подразд.,Должность/профессия,Дата начала,Дата окончан.,Дополнительное соглашение,#cspan,Оклад<br>(руб.),#cspan,Премия<br>(%),#cspan,Персональная<br>надбавка<br>(руб.),#cspan,Доплата за<br>совмещение<br>(руб.),#cspan,Доплата за<br>гостайну<br>(%),#cspan,Доплата за вред.<br>условия труда<br>(%),#cspan,Доплата за<br>классность<br>(%),#cspan,Доплата молодым<br>специалистам<br>(руб.),#cspan,Районный<br>коэффициент<br>(%),#cspan");//Наименования заголовков
			dhxGridEditListPers.attachHeader("#rspan,#rspan,#rspan,#rspan,#rspan,#rspan,#rspan,текущ.,новое,текущ.,новое,текущ.,новое,текущ.,новое,текущ.,новое,текущ.,новое,текущ.,новое,текущ.,новое,текущ.,новое,текущ.,новое");
			dhxGridEditListPers.attachHeader("#master_checkbox,,,#select_filter_strict,#select_filter_strict,,,<BUTTON style='font-size:11px;' title='Установить текущие значения' onClick='script: restoreVal(8);'><img src='dhtmlxSuite/dhtmlxGrid/codebase/imgs/arrow_10_10.gif'></BUTTON>,#master_checkbox,<BUTTON style='font-size:11px;' title='Установить текущие значения' onClick='script: restoreVal(10);'><img src='dhtmlxSuite/dhtmlxGrid/codebase/imgs/arrow_10_10.gif'></BUTTON>,<input type='checkbox' id='ch_opl_1'/>,<BUTTON style='font-size:11px;' title='Установить текущие значения' onClick='script: restoreVal(12);'><img src='dhtmlxSuite/dhtmlxGrid/codebase/imgs/arrow_10_10.gif'></BUTTON>,<input type='checkbox' id='ch_opl_2'/>,<BUTTON style='font-size:11px;' title='Установить текущие значения' onClick='script: restoreVal(14);'><img src='dhtmlxSuite/dhtmlxGrid/codebase/imgs/arrow_10_10.gif'></BUTTON>,<input type='checkbox' id='ch_opl_3'/>,<BUTTON style='font-size:11px;' title='Установить текущие значения' onClick='script: restoreVal(16);'><img src='dhtmlxSuite/dhtmlxGrid/codebase/imgs/arrow_10_10.gif'></BUTTON>,<input type='checkbox' id='ch_opl_4'/>,<BUTTON style='font-size:11px;' title='Установить текущие значения' onClick='script: restoreVal(18);'><img src='dhtmlxSuite/dhtmlxGrid/codebase/imgs/arrow_10_10.gif'></BUTTON>,<input type='checkbox' id='ch_opl_5'/>,<BUTTON style='font-size:11px;' title='Установить текущие значения' onClick='script: restoreVal(20);'><img src='dhtmlxSuite/dhtmlxGrid/codebase/imgs/arrow_10_10.gif'></BUTTON>,<input type='checkbox' id='ch_opl_6'/>,<BUTTON style='font-size:11px;' title='Установить текущие значения' onClick='script: restoreVal(22);'><img src='dhtmlxSuite/dhtmlxGrid/codebase/imgs/arrow_10_10.gif'></BUTTON>,<input type='checkbox' id='ch_opl_7'/>,<BUTTON style='font-size:11px;' title='Установить текущие значения' onClick='script: restoreVal(24);'><img src='dhtmlxSuite/dhtmlxGrid/codebase/imgs/arrow_10_10.gif'></BUTTON>,<input type='checkbox' id='ch_opl_8'/>,<BUTTON style='font-size:11px;' title='Установить текущие значения' onClick='script: restoreVal(26);'><img src='dhtmlxSuite/dhtmlxGrid/codebase/imgs/arrow_10_10.gif'></BUTTON>,<input type='checkbox' id='ch_opl_9'/>");
			dhxGridEditListPers.setInitWidths("30,45,100,80,*,70,70,60,60,60,60,60,60,60,60,60,60,60,60,60,60,60,60,60,60,60,60");//Ширина столбцов
			dhxGridEditListPers.setColTypes("ch,ro,ro,ro,ro,ro,ro,ch,ch,ro,ed,ro,ed,ro,ed,ro,ed,ro,ed,ro,ed,ro,ed,ro,ed,ro,ed");
			dhxGridEditListPers.setColAlign("center,center,left,center,left,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center");
			dhxGridEditListPers.enableTooltips("false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false");
			dhxGridEditListPers.setColumnColor(",,,,,Gainsboro,Gainsboro,,LightCyan,,LemonChiffon,,LemonChiffon,,LemonChiffon,,LemonChiffon,,LemonChiffon,,LemonChiffon,,LemonChiffon,,LemonChiffon,,LemonChiffon");
			dhxGridEditListPers.setImagePath("dhtmlxSuite/dhtmlxGrid/codebase/imgs/");
			dhxGridEditListPers.setSkin(grid_skin);//Оформление
			dhxGridEditListPers.init();
			
			dhxGridEditListPers.clearAndLoad("windows/nar/getGrid_list_edit.php?date="+editListPersToolbar.getValue("input_date"));
		}
}