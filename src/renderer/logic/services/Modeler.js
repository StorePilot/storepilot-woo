/**
 * Shares the modeling constructors through the global StorePilot environment
 */
import * as importer from './Importer'
export default class Modeler {
  constructor () {
    this.Media = importer.Media
    this.Product = importer.Product
    this.ProductCategory = importer.ProductCategory
    this.ProductShippingClass = importer.ProductShippingClass
    this.ProductTag = importer.ProductTag
    this.ProductVariation = importer.ProductVariation
    this.ProductAttribute = importer.ProductAttribute
    this.ProductAttributeTerm = importer.ProductAttributeTerm
    this.Settings = importer.Settings
    this.SettingsProduct = importer.SettingsProduct
    this.TaxClass = importer.TaxClass
  }
}
