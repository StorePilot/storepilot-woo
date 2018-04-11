import Vue from 'vue'
import App from './App'
import quilleditor from 'vue-quill-editor'
import fullscreen from 'vue-fullscreen'
import router from './router'
import storepilot from './logic/storepilot'
import contextmenu from 'vue-context-menu'
import { papir, Controller } from 'papir'
import lazyload from 'vue-lazyload'
import conf from './logic/api/conf.json'
import './assets/wordpress-theme/index.css'
import 'quill/dist/quill.snow.css'
import {
  Upload,
  Pagination,
  Dialog,
  Autocomplete,
  Dropdown,
  DropdownMenu,
  DropdownItem,
  Menu,
  Submenu,
  MenuItem,
  MenuItemGroup,
  Input,
  InputNumber,
  Radio,
  Checkbox,
  Switch,
  Select,
  Option,
  OptionGroup,
  Button,
  ButtonGroup,
  DatePicker,
  Popover,
  Breadcrumb,
  BreadcrumbItem,
  Tabs,
  TabPane,
  Tag,
  Tree,
  Alert,
  Slider,
  Icon,
  Row,
  Col,
  Card,
  Rate,
  Carousel,
  CarouselItem,
  Form,
  FormItem,
  Table,
  TableColumn,
  Loading
} from 'element-ui'
import lang from 'element-ui/lib/locale/lang/en'
import locale from 'element-ui/lib/locale'

locale.use(lang)

Vue.use(Upload)
Vue.use(Pagination)
Vue.use(Dialog)
Vue.use(Autocomplete)
Vue.use(Dropdown)
Vue.use(DropdownMenu)
Vue.use(DropdownItem)
Vue.use(Menu)
Vue.use(Submenu)
Vue.use(MenuItem)
Vue.use(MenuItemGroup)
Vue.use(Input)
Vue.use(InputNumber)
Vue.use(Radio)
Vue.use(Checkbox)
Vue.use(Switch)
Vue.use(Select)
Vue.use(Option)
Vue.use(OptionGroup)
Vue.use(Button)
Vue.use(ButtonGroup)
Vue.use(DatePicker)
Vue.use(Popover)
Vue.use(Breadcrumb)
Vue.use(BreadcrumbItem)
Vue.use(Tabs)
Vue.use(TabPane)
Vue.use(Tag)
Vue.use(Tree)
Vue.use(Alert)
Vue.use(Slider)
Vue.use(Icon)
Vue.use(Row)
Vue.use(Col)
Vue.use(Card)
Vue.use(Rate)
Vue.use(Carousel)
Vue.use(CarouselItem)
Vue.use(Form)
Vue.use(FormItem)
Vue.use(Table)
Vue.use(TableColumn)
Vue.use(Loading.directive)
Vue.prototype.$loading = Loading.service

let apiConf = {}
let dev = process.env.NODE_ENV === 'development'
if (!dev) {
  apiConf.authentication = 'nonce'
  apiConf.nonceTale = '_wpnonce=wcApiSettings.nonce'
}
let apiController = new Controller({ config: apiConf, apis: conf })
Vue.use(quilleditor)
Vue.use(lazyload)
Vue.use(fullscreen)
Vue.use(contextmenu)
Vue.use(papir, { controller: apiController })
Vue.use(storepilot, { dev: dev, controller: apiController })

// if (!process.env.IS_WEB) Vue.use(require('vue-electron'))
Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue({
  components: { App },
  router,
  template: '<App/>'
}).$mount('#storepilot')
