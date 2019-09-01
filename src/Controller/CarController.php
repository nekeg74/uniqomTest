<?php

namespace App\Controller;

use App\Entity\Car;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class CarController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     *
     */
    public function index()
    {

        $cars = $this->getDoctrine()->getRepository(Car::class)->findAll();
        return $this->render('car/index.html.twig', array('cars' => $cars));
    }


    /**
     * @Route("/save")
     */

    public function save()
    {

        $entityManager = $this->getDoctrine()->getManager();
        $car = new Car();
        $car->setBrand('toyota');
        $car->setIsLeft(false);
        $car->setModel('prius');
        $entityManager->persist($car);
        $entityManager->flush();
        return new Response("car saved with id " . $car->getId());
    }


    /**
     * @Route("car/new")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request)
    {

        $car = new Car();
        $form = $this->createFormBuilder($car)
            ->add('brand', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('model', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('isLeft', ChoiceType::class, ['choices' => ['yes' => 1, 'no' => 0,],])
            ->add('save', SubmitType::class, array('label' => 'Create', 'attr' => array('class' => 'btn btn-primary mt-3')))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $car = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($car);
            $entityManager->flush();
            return $this->redirectToRoute('index');
        }

        return $this->render('new.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/car/{id}")
     * @param $id
     * @return Response
     */

    public function show($id)
    {

        $car = $this->getDoctrine()->getRepository(Car::class)->find($id);
        return $this->render('show.html.twig', array('car' => $car));

    }

    /**
     * @Route("delete/{id}")
     * @param $id
     * @return Response
     */

    public function delete($id)
    {

        $car = $this->getDoctrine()->getRepository(Car::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($car);
        $entityManager->flush();
        return $this->render('delete.html.twig', array('car' => $car));

    }


    /**
     * @Route("/loadtable")
     */
    public function rend()
    {
        return $this->render('import.html.twig');
    }

    /**
     * @Route("/completeUpload",name="upload")
     * @param Request $request
     * @return RedirectResponse
     */
    public function load(Request $request)
    {

        $file = $request->files->get("csvFile");
        $cars = array();
        $file->move($this->getParameter('upoads_directory'), "temp.csv");
        if (($fp = fopen($this->getParameter('upoads_directory') . "/temp.csv", "r"))) {
            while (($row = fgetcsv($fp, 1000, ","))) {
                $car = new Car();
                $car->setBrand($row[0]);
                $car->setModel($row[1]);
                if ($row[2] === "l") {
                    $car->setIsLeft(true);
                } else {
                    if ($row[2] === "r") {

                        $car->setIsLeft(false);
                    }
                }
                array_push($cars, $car);
            }

        }
        fclose($fp);

        $entityManager = $this->getDoctrine()->getManager();
        for ($i = 0;
             $i < count($cars);
             $i++) {
            $entityManager->persist($cars[$i]);
            $entityManager->flush();
        }

        return $this->redirectToRoute('index');

    }

    /**
     * @Route("/edit/{id}", methods={"GET","POST"}, name="edit_car")
     */
    public
    function edit(Request $request, $id)
    {
        $car = new Car();
        $car = $this->getDoctrine()->getRepository(Car::class)->find($id);


        $form = $this->createFormBuilder($car)
            ->add('brand', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('model', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('isLeft', ChoiceType::class, ['choices' => ['yes' => 1, 'no' => 0,],])
            ->add('save', SubmitType::class, array('label' => 'Save changes', 'attr' => array('class' => 'btn btn-primary mt-3')))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('index');
        }

        return $this->render('edit.html.twig', array('form' => $form->createView()));
    }


}
