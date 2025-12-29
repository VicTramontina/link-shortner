import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import LinkForm from '../../src/components/LinkForm.vue'
import BaseButton from '../../src/components/BaseButton.vue'

describe('LinkForm', () => {
  const mountOptions = {
    global: {
      stubs: { teleport: true },
      components: { BaseButton }
    }
  }

  it('renders when show is true', () => {
    const wrapper = mount(LinkForm, {
      props: { show: true, link: null },
      ...mountOptions
    })
    expect(wrapper.find('form').exists()).toBe(true)
  })

  it('does not render when show is false', () => {
    const wrapper = mount(LinkForm, {
      props: { show: false, link: null },
      ...mountOptions
    })
    expect(wrapper.find('form').exists()).toBe(false)
  })

  it('shows "Criar Novo Link" title when creating new link', () => {
    const wrapper = mount(LinkForm, {
      props: { show: true, link: null },
      ...mountOptions
    })
    expect(wrapper.text()).toContain('Criar Novo Link')
  })

  it('shows "Editar Link" title when editing existing link', () => {
    const wrapper = mount(LinkForm, {
      props: {
        show: true,
        link: { id: 1, original_url: 'https://example.com', slug: 'abc123', title: 'Test' }
      },
      ...mountOptions
    })
    expect(wrapper.text()).toContain('Editar Link')
  })

  it('populates form with link data when editing', async () => {
    const link = {
      id: 1,
      original_url: 'https://example.com',
      slug: 'abc123',
      title: 'Test Link',
    }
    const wrapper = mount(LinkForm, {
      props: { show: true, link },
      ...mountOptions
    })

    await wrapper.vm.$nextTick()

    const urlInput = wrapper.find('input[type="url"]')
    const slugInput = wrapper.find('input[placeholder="meu-link"]')
    const titleInput = wrapper.find('input[placeholder="Título do meu link"]')

    expect(urlInput.element.value).toBe('https://example.com')
    expect(slugInput.element.value).toBe('abc123')
    expect(titleInput.element.value).toBe('Test Link')
  })

  it('emits close event when cancel button is clicked', async () => {
    const wrapper = mount(LinkForm, {
      props: { show: true, link: null },
      ...mountOptions
    })
    // Find the secondary variant button (cancel button)
    const buttons = wrapper.findAll('button')
    const cancelButton = buttons.find(b => b.classes().includes('border-gray-300'))
    await cancelButton.trigger('click')
    expect(wrapper.emitted('close')).toBeTruthy()
  })

  it('emits close event when backdrop is clicked', async () => {
    const wrapper = mount(LinkForm, {
      props: { show: true, link: null },
      ...mountOptions
    })
    await wrapper.find('.bg-black\\/50').trigger('click')
    expect(wrapper.emitted('close')).toBeTruthy()
  })

  it('emits submit event with form data when form is submitted', async () => {
    const wrapper = mount(LinkForm, {
      props: { show: true, link: null },
      ...mountOptions
    })

    const urlInput = wrapper.find('input[type="url"]')
    const slugInput = wrapper.find('input[placeholder="meu-link"]')
    const titleInput = wrapper.find('input[placeholder="Título do meu link"]')

    await urlInput.setValue('https://example.com')
    await slugInput.setValue('myslug')
    await titleInput.setValue('My Link')

    await wrapper.find('form').trigger('submit.prevent')

    expect(wrapper.emitted('submit')).toBeTruthy()
    expect(wrapper.emitted('submit')[0][0]).toEqual({
      id: undefined,
      data: {
        original_url: 'https://example.com',
        slug: 'myslug',
        title: 'My Link',
      }
    })
  })

  it('shows Criar button text for new link', () => {
    const wrapper = mount(LinkForm, {
      props: { show: true, link: null },
      ...mountOptions
    })
    const submitButton = wrapper.find('button[type="submit"]')
    expect(submitButton.text()).toBe('Criar')
  })

  it('shows Atualizar button text for existing link', () => {
    const wrapper = mount(LinkForm, {
      props: {
        show: true,
        link: { id: 1, original_url: 'https://example.com', slug: 'abc', title: '' }
      },
      ...mountOptions
    })
    const submitButton = wrapper.find('button[type="submit"]')
    expect(submitButton.text()).toBe('Atualizar')
  })
})
