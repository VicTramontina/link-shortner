import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import LinkForm from '../../src/components/LinkForm.vue'

describe('LinkForm', () => {
  it('renders when show is true', () => {
    const wrapper = mount(LinkForm, {
      props: { show: true, link: null },
      global: {
        stubs: { teleport: true }
      }
    })
    expect(wrapper.find('form').exists()).toBe(true)
  })

  it('does not render when show is false', () => {
    const wrapper = mount(LinkForm, {
      props: { show: false, link: null },
      global: {
        stubs: { teleport: true }
      }
    })
    expect(wrapper.find('form').exists()).toBe(false)
  })

  it('shows "Create New Link" title when creating new link', () => {
    const wrapper = mount(LinkForm, {
      props: { show: true, link: null },
      global: {
        stubs: { teleport: true }
      }
    })
    expect(wrapper.text()).toContain('Create New Link')
  })

  it('shows "Edit Link" title when editing existing link', () => {
    const wrapper = mount(LinkForm, {
      props: {
        show: true,
        link: { id: 1, original_url: 'https://example.com', slug: 'abc123', title: 'Test' }
      },
      global: {
        stubs: { teleport: true }
      }
    })
    expect(wrapper.text()).toContain('Edit Link')
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
      global: {
        stubs: { teleport: true }
      }
    })

    await wrapper.vm.$nextTick()

    const urlInput = wrapper.find('input[type="url"]')
    const slugInput = wrapper.find('input[placeholder="my-custom-url"]')
    const titleInput = wrapper.find('input[placeholder="My Link Title"]')

    expect(urlInput.element.value).toBe('https://example.com')
    expect(slugInput.element.value).toBe('abc123')
    expect(titleInput.element.value).toBe('Test Link')
  })

  it('emits close event when cancel button is clicked', async () => {
    const wrapper = mount(LinkForm, {
      props: { show: true, link: null },
      global: {
        stubs: { teleport: true }
      }
    })
    await wrapper.find('button[type="button"]').trigger('click')
    expect(wrapper.emitted('close')).toBeTruthy()
  })

  it('emits close event when backdrop is clicked', async () => {
    const wrapper = mount(LinkForm, {
      props: { show: true, link: null },
      global: {
        stubs: { teleport: true }
      }
    })
    await wrapper.find('.bg-black\\/50').trigger('click')
    expect(wrapper.emitted('close')).toBeTruthy()
  })

  it('emits submit event with form data when form is submitted', async () => {
    const wrapper = mount(LinkForm, {
      props: { show: true, link: null },
      global: {
        stubs: { teleport: true }
      }
    })

    const urlInput = wrapper.find('input[type="url"]')
    const slugInput = wrapper.find('input[placeholder="my-custom-url"]')
    const titleInput = wrapper.find('input[placeholder="My Link Title"]')

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

  it('shows Create button text for new link', () => {
    const wrapper = mount(LinkForm, {
      props: { show: true, link: null },
      global: {
        stubs: { teleport: true }
      }
    })
    const submitButton = wrapper.find('button[type="submit"]')
    expect(submitButton.text()).toBe('Create')
  })

  it('shows Update button text for existing link', () => {
    const wrapper = mount(LinkForm, {
      props: {
        show: true,
        link: { id: 1, original_url: 'https://example.com', slug: 'abc', title: '' }
      },
      global: {
        stubs: { teleport: true }
      }
    })
    const submitButton = wrapper.find('button[type="submit"]')
    expect(submitButton.text()).toBe('Update')
  })
})
