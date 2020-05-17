<?php

namespace App\Controller;
use App\Entity\Product;
use App\Form\ProductType;
use Dompdf\Dompdf;
use Dompdf\Options;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class ProductsController extends AbstractController
{
    public function getProduitByCodeProduit($codeProduit){
        $client = new \GuzzleHttp\Client();

        $response = $client->get('https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/produits/'.$codeProduit);
        $status =$response->getStatusCode();

        $response = $response->getBody()->getContents();

        $Product = (array)(json_decode($response));
        return $Product;

    }

    public function postComposant($ComposantJson){
        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', 'https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/composants', [
            'body' => $ComposantJson
        ]);

        $status =$response->getStatusCode();
        return $status;

    }


    public function postComposition($CompositionJson){

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/compositionsProduit', [
            'body' => $CompositionJson
        ]);

        $status =$response->getStatusCode();
        return $status;
    }

    public function getComposants(){

        $client = new \GuzzleHttp\Client();

        $response = $client->get('https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/composants');
        $status =$response->getStatusCode();

        $response = $response->getBody()->getContents();

        $composants = (array)(json_decode($response));

        $finalComposants = array();


        foreach($composants as $composant)
        {
            $composant = (array) $composant;
            $finalComposants[] = $composant;
        }


        return $finalComposants;
    }

    public function getComposantProduit($codeProduit){
        $client = new \GuzzleHttp\Client();

        $response = $client->get('https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/compositionsProduit');
        $status =$response->getStatusCode();

        $response = $response->getBody()->getContents();

        $Allcompositions = (array)(json_decode($response));

        $compositions = array();

        foreach($Allcompositions as $ac)
        {
            $ac = (array) $ac;
            if( $ac['codeProduit'] == $codeProduit )

            {
                array_shift($ac);
                array_shift($ac);

                $response = $client->get('https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/composants/'. $ac['idComposant']);

                $response  = $response->getBody()->getContents();

                $composant= (array)(json_decode($response));

                $ac['nomComp'] = $composant['nomComp'];
                $ac['unite'] = $composant['unite'];

                $compositions[] = $ac;
            }

        }


        return $compositions;
    }

    public function putProduct($ProductJson , $codeProduit){

        $client = new \GuzzleHttp\Client();
        $response = $client->request('PUT', 'https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/produits/'. $codeProduit,
            [
                'body' => $ProductJson
            ]);

        $status =$response->getStatusCode();
        return $status;
    }


    public function getProducts(){

        $client = new \GuzzleHttp\Client();

        $response = $client->get('https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/produits');

        $status =$response->getStatusCode();

        $response = $response->getBody()->getContents();

        $Produits = (array)(json_decode($response));

        $finalProducts = array();

        foreach($Produits as $Produit)
        {
            $Produit =(array) $Produit;
            $finalProducts[] = $Produit;
        }


        return $finalProducts;
    }


    /**
     * @Route("/content/Products", name="products")
     */
    public function products()
    {

        $Produits = $this->getProducts();

        return $this->render('content/Produits.html.twig', [
            'Products'=> $Produits
        ]);
    }

    /**
     * @Route("/Products/delete/{codeProduit}", name="product_delete")
     */

    public function delete(Request $request, $codeProduit) : Response{
        $client = new \GuzzleHttp\Client();
        $response = $client->delete('https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/produits/'.$codeProduit);

        if($response->getStatusCode() == 200);
        return $this->json(['code' => 200 , 'message' => 'Produit supprimé avec succes'] , 200);


    }


    /**
     * @Route("/Products/modify/{codeProduit}" , name="product_modify")
     */

    public function modify(Request $request, $codeProduit) : Response{

        $Produit = new Product();

        $Mod = $this->getProduitByCodeProduit($codeProduit);

        $form = $this->createForm(ProductType::class,$Produit);

            $form->handleRequest($request);


        if ($form->isSubmitted() ){


           $Produit= $Produit->toArray();
           array_shift($Produit);
           $Produit['codeProduit'] = $codeProduit;
            $serializer = $this->container->get('serializer');
            $Produit = $serializer->serialize($Produit, 'json');

            $status = $this->putProduct($Produit,$codeProduit);


            return $this->redirectToRoute('products');

             return $this->json(['code'=> 200 , 'message'=> 'product modified', 'Produit' => $Produit], 200);
        }


        return $this->render('modals/ProductModifyModal.html.twig',[
            'form_modify' => $form->createView(),
            'codeProduit'=> $codeProduit,
            'Mod' => $Mod]);

    }



    /**
     * @Route("/Products/{codeProduit}/{nomProduit}/Composition" , name="composition")
     */

    public function compose($codeProduit , $nomProduit){

       $composants = $this->getComposantProduit($codeProduit);


        return $this->render('modals/compositions.html.twig',[
            'codeProduit'=> $codeProduit ,
            'composants' => $composants,
            'nomProduit' => $nomProduit]);

    }


    /**
     * @Route("/Products/{codeProduit}/{nomProduit}/Composition/print" , name="print_composition")
     */

    public function print($codeProduit , $nomProduit){

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');


        $dompdf = new Dompdf($pdfOptions);

        $composants = $this->getComposantProduit($codeProduit);

        $composants['nomProduit']= $nomProduit;

        $html = $this->renderView('modals/compositions.html.twig',[
            'codeProduit'=> $codeProduit , 'composants' => $composants]
        );

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $dompdf->stream("mypdf.pdf", [
            "Attachment" => true
        ]);


    }



    public function addcomposant($codeProduit,$Produit)
    {

        dump($Produit);
       $composants =  $this->getComposants();
        return $this->render("content/addComposant.html.twig",[
            'codeProduit'=> $codeProduit ,
            'product'=> $Produit,
            'composants'=> $composants
        ]);

    }

    /**
     * @Route("/Products/add/composant" , name= "post_composant")
     */

    public function composant(Request $request){

        if($request->isXmlHttpRequest()) {
            $composant = $request->getContent();

           $status = $this->postComposant($composant);

           if($status == 201)
            return $this->json(['code'=> 200 , 'message'=> 'composant posted', 'composant'=> $composant], 200);
          else
              return $this->json(['code'=> 200 , 'message'=> 'composant existe déja'], 400);

        }

        return $this->json(['code'=> 200 , 'message'=> 'prblm :('], 200);
    }




    /**
     * @Route("/Products/add/{codeProduit}/composant/post" , name= "post_compositions")
     */

    public function compositionspost(Request $request, $codeProduit){

        if($request->isXmlHttpRequest()){
            $myarray = $request->getContent();

           $myarray = json_decode($myarray,true);


            for ($i = 0; $i < count($myarray); $i++) {

                $Composition = [
                    'idComposition' => null ,
                    'codeProduit' => $codeProduit ,
                    'idComposant' => $myarray[$i]['idComposant'] ,
                    'quantiteComp' => $myarray[$i]['quantiteComp']
                ];

                $Composition = json_encode($Composition);
                $status= $this->postComposition($Composition);
            }
            return $this->json(['code'=> 200 , 'message'=> 'compositions posted','myarray'=>$myarray], 200);
        }

            return $this->json(['code'=> 200 , 'message'=> 'prblm :('], 200);

    }

    /**
     * @Route("/Products/add" , name= "add_product")
     */

    public function addProduct(Request $request)
    {

        $Produit = new Product();


        $form = $this->createForm(ProductType::class, $Produit);
        $form->handleRequest($request);


        if($form->isSubmitted() and $form->isValid()){

            //$codeProduit = getCount();

            $codeProduit = 0;
            return $this->addcomposant($codeProduit,$Produit);



        }

        return $this->render("content/addProduct.html.twig", ['form_product' => $form->createView()]);
    }



}