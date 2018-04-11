import Endpoint from 'papir/src/logic/form/endpoint'
import Prop from 'papir/src/logic/form/prop'

/**
 * Media
 */
export default class Media extends Endpoint {
  constructor (controller, id = null, apiSlug = controller.default, predefined = {}) {
    /**
     * Pass to Endpoint model
     */
    super('media', controller, apiSlug, predefined)

    /**
     * Define properties
     * @note Props fetched which is not defined is available, but is not recognized in code editors
     */
    this.id = new Prop(this, 'id', id)
    this.alt_text = new Prop(this, 'alt_text')
    this.author = new Prop(this, 'author')
    this.caption = new Prop(this, 'caption')
    this.comment_status = new Prop(this, 'comment_status')
    this.date = new Prop(this, 'date')
    this.date_gmt = new Prop(this, 'date_gmt')
    this.description = new Prop(this, 'description')
    this.guid = new Prop(this, 'guid')
    this.link = new Prop(this, 'link')
    this.media_details = new Prop(this, 'media_details')
    this.media_type = new Prop(this, 'media_type')
    this.mime_type = new Prop(this, 'mime_type')
    this.modified = new Prop(this, 'modified')
    this.modified_gmt = new Prop(this, 'modified_gmt')
    this.ping_status = new Prop(this, 'ping_status')
    this.post = new Prop(this, 'post')
    this.slug = new Prop(this, 'slug')
    this.source_url = new Prop(this, 'source_url')
    this.status = new Prop(this, 'status')
    this.template = new Prop(this, 'template')
    this.title = new Prop(this, 'title')
    this.type = new Prop(this, 'type')
  }
}
