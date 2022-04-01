<?php

use Phalcon\Mvc\Controller;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Acl\Component;
use Phalcon\Acl\Role;


class SecureController extends Controller

{
    public function indexAction()
    {

        $roles = new Roles();
        if ($this->request->getPost()) {
            $roles->assign(
                $this->request->getPost(),
                'role'
            );
            $success = $roles->save();
            $this->view->success = $success;
            // $this->view->users = Users::find();
        }
    }

    public function BuildAction()
    {
        $data = Permission::find();
        // echo "<pre>";
        // print_r(json_encode($data));
        // echo "</pre>";Z
        // die;
        $aclFile = APP_PATH . '/security/acl.cache';
        if (true !== is_file($aclFile)) {
            // acl does not exist build it
            $acl = new Memory();
            foreach ($data as $k => $v) {
                $acl->addRole($v->role);
                $acl->addComponent($v->controller, json_decode($v->action));
                $acl->allow($v->role, $v->controller, json_decode($v->action));
            }
            // print_r(json_encode($acl));

            // $acl->addRole('admin');
            // $acl->addRole('customer');
            // $acl->addRole('guest');

            // $acl->addComponent(
            //     'test',
            //     [
            //         'eventtest'
            //     ]
            // );

            // $acl->allow('admin', 'test', 'eventtest');

            // $acl->allow('guest', 'test', 'eventtest');

            file_put_contents(
                $aclFile,
                serialize($acl)
            );
        } else {
            $acl = unserialize(
                file_get_contents($aclFile)
            );
        }

        // if (true == $acl->isAllowed('admin', 'test', 'eventtest')) {
        //     echo "Access Granted";
        // } else {
        //     echo "Access Denied";
        // }
    }
    public function componentsAction()
    {
        $controller = new Components();


        if ($this->request->getPost()) {
            $controller->assign(
                $this->request->getPost(),
                'controller'
            );

            $success = $controller->save();
            $this->view->success = $success;
            // $this->view->users = Users::find();
        }
    }
    public function actionsAction()
    {
        $this->view->dropdown = Components::find();
    }
    public function addactionAction()
    {
        $action = new Actions();
        $action->assign(
            $this->request->getPost(),
            [

                'action',
                'id'
            ]
        );
        // print_r((json_encode($action)));
        // die;
        $action->save();
       $this->response->redirect('/index');
    }
    public function permissionAction()
    {
        $this->view->role = Roles::find();
        $this->view->controller = Components::find();
        // $this->view->action = Action::find();
    }

    public function addpermissionAction()
    {
        $data = $this->request->getPost();
       

        $count = count($_POST);
        $action = array_slice($_POST, 2, $count - 3);

        $actions = array();
        foreach ($action as $k => $v) {
            array_push($actions, $v);
        }
        $fill = new Permission();
        $actions = json_encode($actions);
        $value = array(
            'role' => $data['roles'],
            'controller' => $data['controller'],
            'action' => $actions
        );
        $fill->assign(
            $value,
            [
                'role',
                'controller',
                'action'
            ]
        );
        $fill->save();
        $this->response->redirect('/secure/permission');
    }
}
