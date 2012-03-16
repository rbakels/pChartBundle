<?php
 /*
  * This file is part of the pChart Symfony 2 bundle
  *
  * (c) 2008 - 2012 Xlab B.V.
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

namespace Xlab\pChartBundle\Controller;

use Xlab\pChartBundle\pData;
use Xlab\pChartBundle\pDraw;
use Xlab\pChartBundle\pPie;
use Xlab\pChartBundle\pImage;
use Xlab\pChartBundle\pBarcode39;
use Xlab\pChartBundle\pBarcode128;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/*
 *
 */
class ExamplesController extends Controller
{
  /**
   * ExamplesController::indexAction()
   * 
   * Retrieve a list of available pChart examples for the user to browse through.
   * 
   * @return  void  Nothing
   */
  public function indexAction()
  {
      return $this->render('XlabpChartBundle:Examples:index.html.twig');
  }

  /**
   * ExamplesController::viewAction()
   * 
   * View the selected example, which basically renders the pChart chart.
   * 
   * @return  void  Nothing
   */
  public function viewAction($number)
  {
    $title = "error";

    switch ($number)
    {
      case 1:
        $title = "Example: addRandomValues (example.addRandomValues.php)";
        break;
      case 2:
        $title = "Example: barcode (example.barcode.php)";
        break;
      case 3:
        $title = "Example: barcode39 (example.barcode39.php)";
        break;
      case 4:
        $title = "Example: barcode128 (example.barcode128.php)";
        break;
      case 5:
        $title = "Example: basic (example.basic.php)";
        break;
      case 6:
        $title = "Example: Combo Area Lines (example.Combo.area.lines.php)";
        break;
      case 7:
        $title = "Example: Combo (example.Combo.php)";
        break;
      case 8:
        $title = "Example: draw2DPie (example.draw2DPie.php)";
        break;
      default:
        return $this->redirect($this->generateUrl('pChart_index'));
        break;
    }

    return $this->render('XlabpChartBundle:Examples:view.html.twig', array('number' => $number, "title" => $title));
  }

  /**
   * ExamplesController::exampleAction()
   * 
   * Render the pChart chart example or return a default error message.
   * 
   * @return  void  Nothing
   */
  public function exampleAction($number)
  {
    $response = new Response();
    $response->headers->set('Content-Type', 'image/png');
    $chart = null;
    $function = 'test' . $number;

    if (method_exists($this, $function))
    {
      $chart = $this->$function();
    }
    
    if (is_object($chart) && $chart instanceof pImage)
    {
      /* Capture output and return the response */
      ob_start();
      $chart->autoOutput();
      $response->setContent(ob_get_clean());
    }
    else
    {
      // Render a default error image.
      $image = imagecreate(675, 75);
      $dark_grey = imagecolorallocate($image, 102, 102, 102);
      $white = imagecolorallocate($image, 255, 255, 255);
      $font_path = __DIR__ . "/../Resources/fonts/Silkscreen.ttf";
      $string = 'pChart error';
      imagettftext($image, 50, 0, 10, 60, $white, $font_path, $string);
       
      ob_start();
      imagepng($image);
      $response->setContent(ob_get_clean());

      //Clear up memory 
      imagedestroy($image);
    }

    return $response;
  }

  /**
   * ExamplesController::test1()
   * 
   * Example #1
   * 
   * @note example.addRandomValues
   * 
   * @return  void  Nothing
   */
  protected function test1()
  {
    /* Create the pData object with some random values*/
    $MyData = new pData();  
    $MyData->addRandomValues("Probe 1",array("Values"=>30,"Min"=>0,"Max"=>4));
    $MyData->addRandomValues("Probe 2",array("Values"=>30,"Min"=>6,"Max"=>10));
    $MyData->addRandomValues("Probe 3",array("Values"=>30,"Min"=>12,"Max"=>16));
    $MyData->addRandomValues("Probe 4",array("Values"=>30,"Min"=>18,"Max"=>22));
    $MyData->setAxisName(0,"Probes");

    /* Create the pChart object */
    $myPicture = new pImage(700,230,$MyData);

    /* Create a solid background */
    $Settings = array("R"=>179, "G"=>217, "B"=>91, "Dash"=>1, "DashR"=>199, "DashG"=>237, "DashB"=>111);
    $myPicture->drawFilledRectangle(0,0,700,230,$Settings);

    /* Do a gradient overlay */
    $Settings = array("StartR"=>194, "StartG"=>231, "StartB"=>44, "EndR"=>43, "EndG"=>107, "EndB"=>58, "Alpha"=>50);
    $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,$Settings);
    $myPicture->drawGradientArea(0,0,700,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>100));

    /* Add a border to the picture */
    $myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));

    /* Write the picture title */ 
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/Silkscreen.ttf","FontSize"=>6));
    $myPicture->drawText(10,13,"addRandomValues() :: assess your scales",array("R"=>255,"G"=>255,"B"=>255));

    /* Draw the scale */
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/Forgotte.ttf","FontSize"=>11));
    $myPicture->setGraphArea(50,60,670,190);
    $myPicture->drawFilledRectangle(50,60,670,190,array("R"=>255,"G"=>255,"B"=>255,"Surrounding"=>-200,"Alpha"=>10));
    $myPicture->drawScale(array("CycleBackground"=>TRUE,"LabelSkip"=>4,"DrawSubTicks"=>TRUE));

    /* Graph title */
    $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
    $myPicture->drawText(50,52,"Magnetic noise",array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMLEFT));

    /* Draw the data series */
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/pf_arma_five.ttf","FontSize"=>6));
    $myPicture->drawSplineChart();
    $myPicture->setShadow(FALSE);

    /* Write the legend */
    $myPicture->drawLegend(475,50,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

    return $myPicture;
  }

  /**
   * ExamplesController::test2()
   * 
   * Example #2
   * 
   * @note example.barcode
   * 
   * @return  void  Nothing
   */
  protected function test2()
  {
    /* Create the pChart object */
    $myPicture = new pImage(600,310,NULL,TRUE);

    /* Draw the rounded box */
    $myPicture->setShadow(TRUE,array("X"=>2,"Y"=>2,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>30));  
    $Settings = array("R"=>255,"G"=>255,"B"=>255,"BorderR"=>0,"BorderG"=>0,"BorderB"=>0);
    $myPicture->drawRoundedFilledRectangle(10,10,590,300,10,$Settings);

    /* Draw the cell divisions */
    $myPicture->setShadow(FALSE);  
    $Settings = array("R"=>0,"G"=>0,"B"=>0);
    $myPicture->drawLine(10,110,590,110,$Settings);
    $myPicture->drawLine(200,10,200,110,$Settings);
    $myPicture->drawLine(400,10,400,110,$Settings);
    $myPicture->drawLine(10,160,590,160,$Settings);
    $myPicture->drawLine(220,160,220,200,$Settings);
    $myPicture->drawLine(320,160,320,200,$Settings);
    $myPicture->drawLine(10,200,590,200,$Settings);
    $myPicture->drawLine(400,220,400,300,$Settings);

    /* Write the fields labels */
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/Forgotte.ttf","FontSize"=>10)); 
    $Settings = array("R"=>0,"G"=>0,"B"=>0,"Align"=>TEXT_ALIGN_TOPLEFT);
    $myPicture->drawText(20,20,"FROM",$Settings); 
    $myPicture->drawText(210,20,"TO",$Settings); 
    $myPicture->drawText(20,120,"ACCT.\r\nNUMBER",$Settings); 
    $myPicture->drawText(20,166,"QUANTITY",$Settings); 
    $myPicture->drawText(230,166,"SHIPMENT CODE",$Settings); 
    $myPicture->drawText(330,166,"SENDER CODE",$Settings); 
    $myPicture->drawText(410,220,"MFG DATE",$Settings); 
    $myPicture->drawText(410,260,"NET WEIGTH",$Settings); 

    /* Filling the fields values */
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/Forgotte.ttf","FontSize"=>16)); 
    $myPicture->drawText(70,20,"BEBEER INC\r\n342, MAIN STREET\r\n33000 BORDEAUX\r\nFRANCE",$Settings); 
    $myPicture->drawText(250,20,"MUSTAFA'S BAR\r\n18, CAPITOL STREET\r\n31000 TOULOUSE\r\nFRANCE",$Settings); 

    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/Forgotte.ttf","FontSize"=>35)); 
    $myPicture->drawText(100,120,"2342355552340",$Settings); 

    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/Forgotte.ttf","FontSize"=>20)); 
    $Settings = array("R"=>0,"G"=>0,"B"=>0,"Align"=>TEXT_ALIGN_TOPRIGHT);
    $myPicture->drawText(210,180,"75 CANS",$Settings); 
    $myPicture->drawText(310,180,"TLSE",$Settings); 
    $myPicture->drawText(580,180,"WAREHOUSE#SLOT#B15",$Settings); 

    $Settings = array("R"=>0,"G"=>0,"B"=>0,"Align"=>TEXT_ALIGN_TOPLEFT);
    $myPicture->drawText(410,236,"06/06/2010",$Settings); 
    $myPicture->drawText(410,276,"12.340 Kg",$Settings); 

    /* Create the barcode 39 object */ 
    $Barcode39 = new pBarcode39(__DIR__ . "/../Resources/"); 
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/pf_arma_five.ttf","FontSize"=>6)); 
    $Settings = array("ShowLegend"=>TRUE,"Height"=>55,"DrawArea"=>TRUE,"DrawArea"=>FALSE); 
    $Barcode39->draw($myPicture,"12250000234502",30,220,$Settings); 

    $Settings = array("ShowLegend"=>TRUE,"Height"=>14,"DrawArea"=>TRUE,"DrawArea"=>FALSE); 
    $Barcode39->draw($myPicture,"75 cans",260,220,$Settings); 
    $Barcode39->draw($myPicture,"06062010",260,260,$Settings); 

    /* Create the barcode 128 object */ 
    $Barcode128 = new pBarcode128(__DIR__ . "/../Resources/"); 
    $Settings = array("ShowLegend"=>TRUE,"Height"=>65,"DrawArea"=>TRUE,"DrawArea"=>FALSE); 
    $Barcode128->draw($myPicture,"TLSE",450,25,$Settings); 

    return $myPicture;
  }

  /**
   * ExamplesController::test3()
   * 
   * Example #3
   * 
   * @note example.barcode39
   * 
   * @return  void  Nothing
   */
  protected function test3()
  {
    /* Create the pChart object */
    $myPicture = new pImage(700,230);

    /* Draw the background */
    $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
    $myPicture->drawFilledRectangle(0,0,700,230,$Settings);

    /* Overlay with a gradient */
    $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
    $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,$Settings);
    $myPicture->drawGradientArea(0,0,700,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));

    /* Draw the picture border */
    $myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));

    /* Write the title */
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/Silkscreen.ttf","FontSize"=>6));
    $myPicture->drawText(10,13,"Barcode 39 - Add barcode to your pictures",array("R"=>255,"G"=>255,"B"=>255));

    /* Create the barcode 39 object */
    $Barcode = new pBarcode39(__DIR__ . "/../Resources/");

    /* Draw a simple barcode */
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/pf_arma_five.ttf","FontSize"=>6));
    $Settings = array("ShowLegend"=>TRUE,"DrawArea"=>TRUE);
    $Barcode->draw($myPicture,"pChart Rocks!",50,50,$Settings);

    /* Draw a rotated barcode */
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/Forgotte.ttf","FontSize"=>12));
    $Settings = array("ShowLegend"=>TRUE,"DrawArea"=>TRUE,"Angle"=>90);
    $Barcode->draw($myPicture,"Turn me on",650,50,$Settings);

    /* Draw a rotated barcode */
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/Forgotte.ttf","FontSize"=>12));
    $Settings = array("R"=>255,"G"=>255,"B"=>255,"AreaR"=>150,"AreaG"=>30,"AreaB"=>27,"ShowLegend"=>TRUE,"DrawArea"=>TRUE,"Angle"=>350,"AreaBorderR"=>70,"AreaBorderG"=>20,"AreaBorderB"=>20);
    $Barcode->draw($myPicture,"Do what you want !",290,140,$Settings);

    return $myPicture;
  }

  /**
   * ExamplesController::test4()
   * 
   * Example #4
   * 
   * @note example.barcode128
   * 
   * @return  void  Nothing
   */
  protected function test4()
  {
    /* Create the pChart object */
    $myPicture = new pImage(700,230);

    /* Draw the background */
    $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
    $myPicture->drawFilledRectangle(0,0,700,230,$Settings);

    /* Overlay with a gradient */
    $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
    $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,$Settings);
    $myPicture->drawGradientArea(0,0,700,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));

    /* Draw the border */
    $myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));

    /* Write the title */
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/Silkscreen.ttf","FontSize"=>6));
    $myPicture->drawText(10,13,"Barcode 128 - Add barcode to your pictures",array("R"=>255,"G"=>255,"B"=>255));

    /* Create the barcode 128 object */
    $Barcode = new pBarcode128(__DIR__ . "/../Resources/");

    /* Draw a simple barcode */
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/pf_arma_five.ttf","FontSize"=>6));
    $Settings = array("ShowLegend"=>TRUE,"DrawArea"=>TRUE);
    $Barcode->draw($myPicture,"pChart Rocks!",50,50,$Settings);

    /* Draw a rotated barcode */
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/Forgotte.ttf","FontSize"=>12));
    $Settings = array("ShowLegend"=>TRUE,"DrawArea"=>TRUE,"Angle"=>90);
    $Barcode->draw($myPicture,"Turn me on",650,50,$Settings);

    /* Draw a rotated barcode */
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/Forgotte.ttf","FontSize"=>12));
    $Settings = array("R"=>255,"G"=>255,"B"=>255,"AreaR"=>150,"AreaG"=>30,"AreaB"=>27,"ShowLegend"=>TRUE,"DrawArea"=>TRUE,"Angle"=>350,"AreaBorderR"=>70,"AreaBorderG"=>20,"AreaBorderB"=>20);
    $Barcode->draw($myPicture,"Do what you want !",290,140,$Settings);

    return $myPicture;
  }

  /**
   * ExamplesController::test5()
   * 
   * Example #5
   * 
   * @note example.basic
   * 
   * @return  void  Nothing
   */
  protected function test5()
  {
    /* Create your dataset object */ 
    $myData = new pData(); 

    /* Add data in your dataset */ 
    $myData->addPoints(array(1,3,4,3,5));

    /* Create a pChart object and associate your dataset */ 
    $myPicture = new pImage(700,230,$myData);

    /* Choose a nice font */
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/Forgotte.ttf","FontSize"=>11));

    /* Define the boundaries of the graph area */
    $myPicture->setGraphArea(60,40,670,190);

    /* Draw the scale, keep everything automatic */ 
    $myPicture->drawScale();

    /* Draw the scale, keep everything automatic */ 
    $myPicture->drawSplineChart();

    return $myPicture;
  }

  /**
   * ExamplesController::test6()
   * 
   * Example #6
   * 
   * @note example.Combo.area.lines
   * 
   * @return  void  Nothing
   */
  protected function test6()
  {
    /* Create and populate the pData object */
    $MyData = new pData();  
    $MyData->addPoints(array(4,2,10,12,8,3),"Probe 1");
    $MyData->addPoints(array(3,12,15,8,5,5),"Probe 2");
    $MyData->setSerieTicks("Probe 2",4);
    $MyData->setAxisName(0,"Temperatures");
    $MyData->addPoints(array("Jan","Feb","Mar","Apr","May","Jun"),"Labels");
    $MyData->setSerieDescription("Labels","Months");
    $MyData->setAbscissa("Labels");

    /* Create the pChart object */
    $myPicture = new pImage(700,230,$MyData);

    /* Turn of Antialiasing */
    $myPicture->Antialias = FALSE;

    /* Draw the background */ 
    $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
    $myPicture->drawFilledRectangle(0,0,700,230,$Settings); 

    /* Overlay with a gradient */ 
    $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
    $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,$Settings); 

    /* Add a border to the picture */
    $myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));

    /* Write the chart title */ 
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/Forgotte.ttf","FontSize"=>11));
    $myPicture->drawText(150,35,"Average temperature",array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

    /* Set the default font */
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/pf_arma_five.ttf","FontSize"=>6));

    /* Define the chart area */
    $myPicture->setGraphArea(60,40,650,200);

    /* Draw the scale */
    $scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"GridR"=>255,"GridG"=>255,"GridB"=>255,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
    $myPicture->drawScale($scaleSettings);

    /* Write the chart legend */
    $myPicture->drawLegend(540,20,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

    /* Turn on Antialiasing */
    $myPicture->Antialias = TRUE;

    /* Draw the area chart */
    $MyData->setSerieDrawable("Probe 1",TRUE);
    $MyData->setSerieDrawable("Probe 2",FALSE);
    $myPicture->drawAreaChart();

    /* Draw a line and a plot chart on top */
    $MyData->setSerieDrawable("Probe 2",TRUE);
    $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
    $myPicture->drawLineChart();
    $myPicture->drawPlotChart(array("PlotBorder"=>TRUE,"PlotSize"=>3,"BorderSize"=>1,"Surrounding"=>-60,"BorderAlpha"=>80));

    return $myPicture;
  }

  /**
   * ExamplesController::test7()
   * 
   * Example #7
   * 
   * @note example.Combo
   * 
   * @return  void  Nothing
   */
  protected function test7()
  {
    /* Create the pData object with some random values*/
    $MyData = new pData(); 
    $MyData->addPoints(array(30,24,32),"This year");
    $MyData->addPoints(array(28,20,27),"Last year");
    $MyData->setSerieTicks("Last year",4);
    $MyData->addPoints(array("Year","Month","Day"),"Labels");
    $MyData->setAbscissa("Labels");

    /* Create the pChart object */
    $myPicture = new pImage(700,230,$MyData);

    /* Turn on antialiasing */
    $myPicture->Antialias = FALSE;

    /* Create a solid background */
    $Settings = array("R"=>179, "G"=>217, "B"=>91, "Dash"=>1, "DashR"=>199, "DashG"=>237, "DashB"=>111);
    $myPicture->drawFilledRectangle(0,0,700,230,$Settings);

    /* Do a gradient overlay */
    $Settings = array("StartR"=>194, "StartG"=>231, "StartB"=>44, "EndR"=>43, "EndG"=>107, "EndB"=>58, "Alpha"=>50);
    $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,$Settings);
    $myPicture->drawGradientArea(0,0,700,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>100));

    /* Add a border to the picture */
    $myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));

    /* Write the picture title */ 
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/Silkscreen.ttf","FontSize"=>6));
    $myPicture->drawText(10,13,"Chart title",array("R"=>255,"G"=>255,"B"=>255));

    /* Draw the scale */
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/pf_arma_five.ttf","FontSize"=>6));
    $myPicture->setGraphArea(50,60,670,190);
    $myPicture->drawFilledRectangle(50,60,670,190,array("R"=>255,"G"=>255,"B"=>255,"Surrounding"=>-200,"Alpha"=>10));
    $myPicture->drawScale(array("CycleBackground"=>TRUE));

    /* Graph title */
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/Forgotte.ttf","FontSize"=>11));
    $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
    $myPicture->drawText(50,52,"Chart subtitle",array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMLEFT));

    /* Draw the bar chart chart */
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/pf_arma_five.ttf","FontSize"=>6));
    $MyData->setSerieDrawable("Last year",FALSE);
    $myPicture->drawBarChart();

    /* Turn on antialiasing */
    $myPicture->Antialias = TRUE;

    /* Draw the line and plot chart */
    $MyData->setSerieDrawable("Last year",TRUE);
    $MyData->setSerieDrawable("This year",FALSE);
    $myPicture->setShadow(TRUE,array("X"=>2,"Y"=>2,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
    $myPicture->drawSplineChart();

    $myPicture->setShadow(FALSE);
    $myPicture->drawPlotChart(array("PlotSize"=>3,"PlotBorder"=>TRUE,"BorderSize"=>3,"BorderAlpha"=>20));

    /* Make sure all series are drawable before writing the scale */
    $MyData->drawAll();

    /* Write the legend */
    $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
    $myPicture->drawLegend(580,35,array("Style"=>LEGEND_ROUND,"Alpha"=>20,"Mode"=>LEGEND_HORIZONTAL));

    return $myPicture;
  }

  /**
   * ExamplesController::test8()
   * 
   * Example #8
   * 
   * @note example.draw2DPie
   * 
   * @return  void  Nothing
   */
  protected function test8()
  {
    /* Create and populate the pData object */
    $MyData = new pData();   
    $MyData->addPoints(array(40,60,15,10,6,4),"ScoreA");  
    $MyData->setSerieDescription("ScoreA","Application A");

    /* Define the absissa serie */
    $MyData->addPoints(array("<10","10<>20","20<>40","40<>60","60<>80",">80"),"Labels");
    $MyData->setAbscissa("Labels");

    /* Create the pChart object */
    $myPicture = new pImage(700,230,$MyData);

    /* Draw a solid background */
    $Settings = array("R"=>173, "G"=>152, "B"=>217, "Dash"=>1, "DashR"=>193, "DashG"=>172, "DashB"=>237);
    $myPicture->drawFilledRectangle(0,0,700,230,$Settings);

    /* Draw a gradient overlay */
    $Settings = array("StartR"=>209, "StartG"=>150, "StartB"=>231, "EndR"=>111, "EndG"=>3, "EndB"=>138, "Alpha"=>50);
    $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,$Settings);
    $myPicture->drawGradientArea(0,0,700,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>100));

    /* Add a border to the picture */
    $myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));

    /* Write the picture title */ 
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/Silkscreen.ttf","FontSize"=>6));
    $myPicture->drawText(10,13,"pPie - Draw 2D pie charts",array("R"=>255,"G"=>255,"B"=>255));

    /* Set the default font properties */ 
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/Forgotte.ttf","FontSize"=>10,"R"=>80,"G"=>80,"B"=>80));

    /* Enable shadow computing */ 
    $myPicture->setShadow(TRUE,array("X"=>2,"Y"=>2,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>50));

    /* Create the pPie object */ 
    $PieChart = new pPie($myPicture,$MyData);

    /* Draw a simple pie chart */ 
    $PieChart->draw2DPie(120,125,array("SecondPass"=>FALSE));

    /* Draw an AA pie chart */ 
    $PieChart->draw2DPie(340,125,array("DrawLabels"=>TRUE,"LabelStacked"=>TRUE,"Border"=>TRUE));

    /* Draw a splitted pie chart */ 
    $PieChart->draw2DPie(560,125,array("WriteValues"=>PIE_VALUE_PERCENTAGE,"DataGapAngle"=>10,"DataGapRadius"=>6,"Border"=>TRUE,"BorderR"=>255,"BorderG"=>255,"BorderB"=>255));

    /* Write the legend */
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/pf_arma_five.ttf","FontSize"=>6));
    $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));
    $myPicture->drawText(120,200,"Single AA pass",array("DrawBox"=>TRUE,"BoxRounded"=>TRUE,"R"=>0,"G"=>0,"B"=>0,"Align"=>TEXT_ALIGN_TOPMIDDLE));
    $myPicture->drawText(440,200,"Extended AA pass / Splitted",array("DrawBox"=>TRUE,"BoxRounded"=>TRUE,"R"=>0,"G"=>0,"B"=>0,"Align"=>TEXT_ALIGN_TOPMIDDLE));

    /* Write the legend box */ 
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . "/../Resources/fonts/Silkscreen.ttf","FontSize"=>6,"R"=>255,"G"=>255,"B"=>255));
    $PieChart->drawPieLegend(380,8,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

    return $myPicture;
  }

}

?>