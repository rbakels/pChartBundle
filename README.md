README
======
The pChart Bundle for Symfony 2.x gives you the possibility to use the pChart library for all your charting needs.

This is an initial release and supports the rendering of all chart types. Small modification has been made to the latest release of the pChart library, since it didn't play very nicely together with Symfony 2.x and PHP5.3. pChart caching is not tested at this point. 

This Bundle can be downloaded from https://github.com/rbakels/pChartBundle , but you already knew that.

License
-------
See the LICENSE file included with this bundle.

Author
------
Robin Bakels (robin@xlab.nl). Software Engineer for the Dutch company *Xlab B.V.*. See: http://www.xlab.nl/ for more information

Installation
============
Installation is quite straightforward. Integrating the bundle in your current Symfony 2 installation can be done by adding the following to your *app/AppKernel.php* file:

	new Xlab\pChartBundle\XlabpChartBundle(),

Optionally, you can add the following to your routing.yml:

	pChart:
	  resource: "@XlabpChartBundle/Resources/config/routing_pChart.yml"
	  prefix:   /pChart

This gives you the possibility to a small live preview of some examples, originally released with the pChart source. The list of examples, after inclusion of the routing_pChart.yml file gives you access to the following URL:

	http://<your_hostname>/pChart/index

How to use this bundle?
-----------------------
There are probably more then one way to use this. Currently, I'm quite fond of generating the chart image inside a Controller action and calling this method from your Twig template file within an <img> tag.

1. Add a routing to your routing.yml, where you want to render the chart image from.
2. Include the proper namespaces. The names are taken from the original files (e.g. pPie.class.php is within the Xlab\pChartBundle\pPie namespace) 
3. Create the controller method.
4. Add the following code, after all the pChart specific calls:

Example:

	<?php
	namespace Xlab\pChartBundle\Controller;

	use Xlab\pChartBundle\pData;
	use Xlab\pChartBundle\pDraw;
	use Xlab\pChartBundle\pPie;
	use Xlab\pChartBundle\pImage;
	use Xlab\pChartBundle\pBarcode39;
	use Xlab\pChartBundle\pBarcode128;

	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Response;

	class ExamplesController extends Controller
	{
		public function exampleAction()
		{
			$response = new Response();
			$response->headers->set('Content-Type', 'image/png');

			/* pChart stuff goes here */
			...

			/* Capture output and return the response */
			ob_start();
			$chart->autoOutput();
			$response->setContent(ob_get_clean());

			return $respone;
		}
	}

In the above example, the *$chart* variable is an instance of a pImage class.

See also: *Xlab/pChartBundle/Controller/ExamplesController.php*

Changelog
=========
16 March 2012:

* Initial release