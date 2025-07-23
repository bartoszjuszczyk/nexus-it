<?php

namespace App\Controller;

use App\Entity\Equipment;
use App\Form\Type\EquipmentType;
use App\Repository\EquipmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EquipmentController extends AbstractController
{
    #[Route('/equipment', name: 'app_equipment_list')]
    public function index(EquipmentRepository $equipmentRepository): Response
    {
        $equipment = $equipmentRepository->findAll();

        return $this->render('equipment/index.html.twig', [
            'equipment' => $equipment,
            'appTitle' => $this->getParameter('app_title'),
            'pageTitle' => $this->getParameter('app_title').' – Equipment',
            'boardTitle' => 'Equipment',
        ]);
    }

    #[Route('/equipment/new', name: 'app_equipment_new')]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $equipment = new Equipment();
        $form = $this->createForm(EquipmentType::class, $equipment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($equipment);
                $entityManager->flush();
                $this->addFlash('success', 'Equipment created successfully.');

                return $this->redirectToRoute('app_equipment_list');
            } catch (\Exception $exception) {
                $this->addFlash('error', 'There was an error while creating the equipment.');

                return $this->redirectToRoute('app_equipment_new');
            }
        }

        return $this->render('equipment/new.html.twig', [
            'form' => $form,
            'appTitle' => $this->getParameter('app_title'),
            'pageTitle' => $this->getParameter('app_title').' – Create Equipment',
            'boardTitle' => 'Create Equipment',
        ]);
    }

    #[Route('/equipment/{id}/edit', name: 'app_equipment_edit')]
    public function edit(
        Equipment $equipment,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $form = $this->createForm(EquipmentType::class, $equipment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($equipment);
                $entityManager->flush();
                $this->addFlash('success', 'Equipment updated successfully.');

                return $this->redirectToRoute('app_equipment_list');
            } catch (\Exception $exception) {
                $this->addFlash('error', 'There was an error updating the equipment.');

                return $this->redirectToRoute('app_equipment_edit', ['id' => $equipment->getId()]);
            }
        }

        return $this->render('equipment/edit.html.twig', [
            'form' => $form,
            'appTitle' => $this->getParameter('app_title'),
            'pageTitle' => $this->getParameter('app_title').' – Edit Equipment',
            'boardTitle' => 'Edit Equipment: '.$equipment->getName(),
        ]);
    }

    #[Route('/equipment/{id}/delete', name: 'app_equipment_delete')]
    public function delete(
        Equipment $equipment,
        EntityManagerInterface $entityManager,
    ): Response {
        try {
            $entityManager->remove($equipment);
            $entityManager->flush();
            $this->addFlash('success', 'Equipment deleted successfully.');
        } catch (\Exception $exception) {
            $this->addFlash('error', 'There was an error while deleting the equipment.');
        }

        return $this->redirectToRoute('app_equipment_list');
    }
}
