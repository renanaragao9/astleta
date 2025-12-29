import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-cep', IndexField)
  app.component('detail-cep', DetailField)
  app.component('form-cep', FormField)
})
