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

/**
 * Class ProductsController
 * @package App\Controller
 * @Route("/content")
 */

/*this whole part is missing a lot of shit due to server prblms*/

class ProductsController extends AbstractController
{
    public function getCount(){
        $client = new \GuzzleHttp\Client();

        try{
            $response = $client->get('https://app.167-172-50-144.plesk.page/produits/CountAll/getCount');
            $status =$response->getStatusCode();
            if($status >= 200 && $status<300 )
            {
                $response = $response->getBody()->getContents();
                $count = (array)(json_decode($response));
                return $count['count'];

            }
        }catch(\GuzzleHttp\Exception\RequestException $e){
           return -1;
        }




    }


    public function getProduitByCodeProduit($codeProduit){
        $client = new \GuzzleHttp\Client();

        try{
            $response = $client->get('https://app.167-172-50-144.plesk.page/produits/'.$codeProduit);
            $status =$response->getStatusCode();

            $response = $response->getBody()->getContents();

            $Product = (array)(json_decode($response));
            return $Product;
        }catch(\GuzzleHttp\Exception\RequestException $e){
            $response = ['libelle' => '500'];
            return $response;
        }


    }

    public function postComposant($ComposantJson){
        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', 'https://app.167-172-50-144.plesk.page/composants', [
            'body' => $ComposantJson
        ]);

        $status =$response->getStatusCode();
        return $status;

    }



    public function postComposition($CompositionJson){

        $client = new \GuzzleHttp\Client();

        try{ $response = $client->request('POST', 'https://app.167-172-50-144.plesk.page/compositionsProduit', [
            'body' => $CompositionJson
        ]);

            $status =$response->getStatusCode();
            return $status;
        }catch(\GuzzleHttp\Exception\RequestException $e){

            return 500;

        }


    }

    public function getComposants(){

        $client = new \GuzzleHttp\Client();

        $response = $client->get('https://app.167-172-50-144.plesk.page/composants');
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

        $response = $client->get('https://app.167-172-50-144.plesk.page/compositionsProduit');
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

                $response = $client->get('https://app.167-172-50-144.plesk.page/composants/'. $ac['idComposant']);

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
        $response = $client->request('PUT', 'https://app.167-172-50-144.plesk.page/produits/'. $codeProduit,
            [
                'body' => $ProductJson
            ]);

        $status =$response->getStatusCode();
        return $status;
    }


    public function getProducts(){

        $client = new \GuzzleHttp\Client();

        $response = $client->get('https://app.167-172-50-144.plesk.page/produits');

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
     * @Route("/Products/post/{designation}/{TVA}/{HA}" , name="post.product")
     */

    public function ajaxdidntwannadoit(Request $request, $designation, $TVA, $HA){
        $client = new \GuzzleHttp\Client();
        if($request->isXmlHttpRequest()) {
//            $produitJson = $request->getContent();

            $produit = [    'libelle'=>$designation,
                            'prixHA'=> $HA,
                            'TVA' => $TVA,
                            'prixTTC'=> $TVA+$HA
                          ];

            $theLastOfus = $this->getCount()+1000;

            $lastProduct = $this->getProduitByCodeProduit($theLastOfus);
            dump($lastProduct);
            if( ($lastProduct['libelle'] != $designation) || ($lastProduct['libelle'] == '500') )
            {
                $produitJson=json_encode($produit);

                dump($produitJson);
                $response = $client->request('POST', 'https://app.167-172-50-144.plesk.page/produits', [
                    'body' => $produitJson
                ]);

                $status =$response->getStatusCode();

                if($status >= 201 && $status < 300)
                    return $this->json(['code'=> 200 , 'message'=> 'produit posted', 'composant'=> $produitJson], 200);
                else
                    return $this->json(['code'=> 200 , 'message'=> 'produit existe déja1'], 400);

             }else{
                $var = "mhere";
                dump($var);
                return $this->json(['code'=> 200 , 'message'=> 'produit existe déja2'], 400);

            }


    }else return $this->json(['code'=> 200 , 'message'=> 'produit existe déja3'], 400);}

    /**
     * @Route("/Products", name="products")
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
        $response = $client->delete('https://app.167-172-50-144.plesk.page/produits/'.$codeProduit);

        if(($response->getStatusCode() >= 200) && ($response->getStatusCode() < 300))
            return $this->json(['code' => 200 , 'message' => 'Produit supprimée avec succes'] , 200);
        else
            return $this->json(['code' => $response->getStatusCode() , 'message' => 'oops'] , 500);


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

//       dump($composants);
//       dump($nomProduit);
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

      //  dump($composants);



        $html = $this->renderView('modals/compositions.html.twig',[
            'codeProduit'=> $codeProduit ,
            'composants' => $composants ,
            'nomProduit'=>$nomProduit]
        );

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        return new Response($dompdf->stream("mypdf.pdf", [
            "Attachment" => true
        ]));

//        $dompdf->stream("mypdf.pdf", [
//            "Attachment" => true
//        ]);


    }



    public function addcomposant($codeProduit,$Produit)
    {

//        dump($codeProduit);
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
                    'codeProduit' => [strval($codeProduit)] ,
                    'idComposant' => [$myarray[$i]['idComposant']] ,
                    'quantiteComp' => $myarray[$i]['quantiteComp']
                ];

                $serializer = $this->container->get('serializer');
                $Composition = $serializer->serialize($Composition, 'json');


                $status= $this->postComposition($Composition);
//                dump($status);
            }

            return $this->json(['code'=> 200 , 'message'=> 'compositions posted','myarray'=>$Composition], 200);
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

            if($this->getCount() != -1)
            $codeProduit = $this->getCount()+ 1000;
  else
      $codeProduit = 0;

            return $this->addcomposant($codeProduit,$Produit);



        }

        return $this->render("content/addProduct.html.twig", ['form_product' => $form->createView()]);
    }



}
