<?php

namespace App\Controller\Admin;

use Exception;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Category;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use function PHPUnit\Framework\throwException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    protected $userRepository;
    protected $categoryRepository;
    protected $productRepository;
    protected $orderRepository;
    
    public function __construct(
        UserRepository $userRepository,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        OrderRepository $orderRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
    }

    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'countUsers' => $this->userRepository->countAllUsers(),
            'countCategories' => $this->categoryRepository->countAllCategories(),
            'countProducts' => $this->productRepository->countAllProducts(),
            'countOrders' => $this->orderRepository->countAllOrders(),

        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Floreil Admin Dashboard');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-dashboard');
        yield MenuItem::linkToUrl('Florei', 'fas fa-home', $this->generateUrl('app_main'));

        yield MenuItem::section('Shop');
        yield MenuItem::linkToCrud('Categories', 'fas fa-list', Category::class);
        yield MenuItem::linkToCrud('Products', 'fas fa-list', Product::class);

        yield MenuItem::section('Users');
        yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Orders', 'fas fa-list', Order::class);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        if (!$user instanceof User) {
            throw new Exception('Wrong user');
        }
        return parent::configureUserMenu($user)
            ->setAvatarUrl('images/profiles/' . $user->getAvatarImage())
            ->addMenuItems([
                MenuItem::linkToUrl('Profile', 'fa fa-address-card', $this->generateUrl('app_profile'))
            ]);
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addWebpackEncoreEntry('admin');
    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
