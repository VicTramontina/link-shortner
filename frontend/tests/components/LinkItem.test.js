import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import LinkItem from '../../src/components/LinkItem.vue'

describe('LinkItem', () => {
  const mockLink = {
    id: 1,
    title: 'Test Link',
    original_url: 'https://example.com/very-long-url',
    slug: 'abc123',
    short_url: 'http://localhost/abc123',
    access_count: 42,
  }

  it('renders link title', () => {
    const wrapper = mount(LinkItem, {
      props: { link: mockLink }
    })
    expect(wrapper.text()).toContain('Test Link')
  })

  it('renders "Link sem título" when title is empty', () => {
    const wrapper = mount(LinkItem, {
      props: {
        link: { ...mockLink, title: null }
      }
    })
    expect(wrapper.text()).toContain('Link sem título')
  })

  it('renders short URL', () => {
    const wrapper = mount(LinkItem, {
      props: { link: mockLink }
    })
    expect(wrapper.text()).toContain('http://localhost/abc123')
  })

  it('renders access count', () => {
    const wrapper = mount(LinkItem, {
      props: { link: mockLink }
    })
    expect(wrapper.text()).toContain('42')
  })

  it('emits edit event when edit button is clicked', async () => {
    const wrapper = mount(LinkItem, {
      props: { link: mockLink }
    })
    await wrapper.find('[title="Editar"]').trigger('click')
    expect(wrapper.emitted('edit')).toBeTruthy()
    expect(wrapper.emitted('edit')[0]).toEqual([mockLink])
  })

  it('emits delete event when delete button is clicked', async () => {
    const wrapper = mount(LinkItem, {
      props: { link: mockLink }
    })
    await wrapper.find('[title="Excluir"]').trigger('click')
    expect(wrapper.emitted('delete')).toBeTruthy()
    expect(wrapper.emitted('delete')[0]).toEqual([mockLink])
  })

  it('shows restore and force delete buttons when showRestore is true', () => {
    const wrapper = mount(LinkItem, {
      props: { link: mockLink, showRestore: true }
    })
    expect(wrapper.find('[title="Restaurar"]').exists()).toBe(true)
    expect(wrapper.find('[title="Excluir permanentemente"]').exists()).toBe(true)
    expect(wrapper.find('[title="Editar"]').exists()).toBe(false)
  })

  it('emits restore event when restore button is clicked', async () => {
    const wrapper = mount(LinkItem, {
      props: { link: mockLink, showRestore: true }
    })
    await wrapper.find('[title="Restaurar"]').trigger('click')
    expect(wrapper.emitted('restore')).toBeTruthy()
    expect(wrapper.emitted('restore')[0]).toEqual([mockLink])
  })

  it('emits forceDelete event when force delete button is clicked', async () => {
    const wrapper = mount(LinkItem, {
      props: { link: mockLink, showRestore: true }
    })
    await wrapper.find('[title="Excluir permanentemente"]').trigger('click')
    expect(wrapper.emitted('forceDelete')).toBeTruthy()
    expect(wrapper.emitted('forceDelete')[0]).toEqual([mockLink])
  })

  it('copies URL to clipboard and shows success state', async () => {
    const writeTextMock = vi.fn().mockResolvedValue(undefined)
    Object.defineProperty(navigator, 'clipboard', {
      value: { writeText: writeTextMock },
      writable: true,
      configurable: true,
    })

    const wrapper = mount(LinkItem, {
      props: { link: mockLink }
    })

    await wrapper.find('[title="Copiar URL"]').trigger('click')
    expect(writeTextMock).toHaveBeenCalledWith(mockLink.short_url)
    expect(wrapper.emitted('copy')).toBeTruthy()
  })
})
