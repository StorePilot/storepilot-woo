import Vue from 'vue'
import Router from 'vue-router'

// Views
import License from '../views/License'
import Activate from '../views/Activate'
import Authorize from '../views/Authorize'
import Permalinks from '../views/Permalinks'
import ProductManager from '../views/ProductManager/ProductManager'
import Product from '../views/ProductManager/Editors/Product/Product'
import Products from '../views/ProductManager/Editors/Bulk/Products'
import Category from '../views/ProductManager/Editors/Category/Category'
import Tag from '../views/ProductManager/Editors/Tag/Tag'
import Settings from '../views/ProductManager/Editors/Settings/Settings'

Vue.use(Router)

const router = new Router({
  routes: [
    {
      path: '/',
      redirect: '/manager/product'
    },
    {
      path: '*',
      redirect: '/'
    },
    {
      path: '/license',
      name: 'License',
      component: License,
      props: true
    },
    {
      path: '/activate',
      name: 'Activate',
      component: Activate,
      props: true
    },
    {
      path: '/authorize',
      name: 'Authorize',
      component: Authorize,
      props: true
    },
    {
      path: '/permalinks',
      name: 'Permalinks',
      component: Permalinks,
      props: true
    },
    {
      path: '/manager',
      component: ProductManager,
      children: [
        {
          path: 'product/:id?',
          name: 'Product',
          component: Product,
          props: true
        },
        {
          path: 'products',
          name: 'Products',
          component: Products,
          props: true
        },
        {
          path: 'category/:id?',
          name: 'Category',
          component: Category,
          props: true
        },
        {
          path: 'tag/:id?',
          name: 'Tag',
          component: Tag,
          props: true
        },
        {
          path: 'settings',
          name: 'Settings',
          component: Settings,
          props: true
        }
      ]
    }
  ]
})

export default router
